<?php
namespace Core\Db;

use Core\Db\Query;
use PDOException;
use PDO;
use Core\Db;

abstract class Connection
{

    protected $link = null;

	protected $pdoStatement = null;

	//表名
	protected $table = '';

	protected $query = null;

	protected $queryStr = '';

	// 返回或者影响记录数
    protected $numRows = 0;

    protected $lastInsertId;

	protected $fetchType = PDO::FETCH_ASSOC;

	// 数据库连接参数配置
    protected $config = [
        // 数据库类型
        'type'          => '',
        // 服务器地址
        'hostname'      => '',
        // 数据库名
        'database'      => '',
        // 用户名
        'username'      => '',
        // 密码
        'password'      => '',
        // 端口
        'hostport'      => '',
        // 连接dsn
        'dsn'           => '',
        // 数据库连接参数
        'params'        => [],
        // 数据库编码默认采用utf8
        'charset'       => 'utf8',
        // 数据库表前缀
        'prefix'        => '',
        // 数据库调试模式
        'debug'         => false
    ];

    // PDO连接参数
    protected $params = [
        PDO::ATTR_CASE              => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    ];

    public function __construct($config = '')
    {
    	if(!empty($config)) {
    		$this->config = array_merge($this->config, $config);
    		if (is_array($this->config['params'])) {
                $this->params = $this->config['params'] + $this->params;
            }
    	}
    	$this->query = new Query($this);
    }

    public function connect($config = '')
    {
    	if(empty($this->link)) {
	    	if(!empty($config)) {
	    		$this->config = $config;
	    	}
	    	$params = $this->config['params'] + $this->params;

	    	if(empty($this->config['dsn'])) {
	    		$this->config['dsn'] = $this->parseDsn($this->config);
	    	}

	    	$this->link = new PDO($this->config['dsn'], $this->config['username'], $this->config['password'], $params);
	    }
	    return $this->link;
    }



    public function getTable()
    {
    	return $this->table;
    }

    public function setTable($table)
    {
    	$this->table = $table;
    	return true;
    }

    public function getConfig($config = null)
    {
    	if(empty($config)) {
    		return $this->config;
    	}

    	if(isset($this->config[$config])) {
    		return $this->config[$config];
    	}

    }

    public function getDriverName()
    {
    	return $this->config['type'];
    }

    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $sql  sql指令
     * @param array $bind 参数绑定
     * @param boolean $fetch  不执行只是获取SQL
     * @param bool $returnPdo  是否返回 PDOStatement 对象
     * @return mixed
     */
    public function query($sql, $bind = [], $fetch = false, $returnPdo = false)
    {
    	$this->initLink();

        // 根据参数绑定组装最终的SQL语句
        $this->queryStr = $this->getBindSql($sql, $bind);

        if ($fetch) {
            return $this->queryStr;
        }

        //释放前次的查询结果
        if (!empty($this->PDOStatement)) {
            $this->free();
        }

        Db::$queryTimes++;
        try {
            // 调试开始
            $this->debug(true);
            // 预处理
            $this->PDOStatement = $this->link->prepare($sql);
            // 参数绑定
            $this->bindValue($bind);
            // 执行查询
            $result = $this->PDOStatement->execute();
            // 调试结束
            $this->debug(false);
            return $returnPdo ? $this->PDOStatement : $this->getResult();
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * 执行语句
     * @access public
     * @param string $sql  sql指令
     * @param array $bind 参数绑定
     * @param boolean $fetch  不执行只是获取SQL
     * @return integer
     */
    public function execute($sql, $bind = [], $fetch = false)
    {
    	$this->initLink();

        // 根据参数绑定组装最终的SQL语句
        $this->queryStr = $this->getBindSql($sql, $bind);

        if ($fetch) {
            return $this->queryStr;
        }

        //释放前次的查询结果
        if (!empty($this->PDOStatement)) {
            $this->free();
        }

        Db::$queryTimes++;
        try {
            // 调试开始
            $this->debug(true);
            // 预处理
            $this->PDOStatement = $this->link->prepare($sql);
            // 参数绑定
            $this->bindValue($bind);
            // 执行查询
            $result = $this->PDOStatement->execute();

            $this->numRows = $this->PDOStatement->rowCount();
            // 调试结束
            $this->debug(false);

            if (preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $sql)) {
                $this->lastInsertId = $this->link->lastInsertId();
            }
            return $this->numRows;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function initLink()
    {
    	if(is_null($this->link)) {
    		$this->connect();
    	}
    	return $this->link;
    }

    /**
     * 参数绑定
     * 支持 ['name'=>'value','id'=>123] 对应命名占位符
     * 或者 ['value',123] 对应问号占位符
     * @access public
     * @param array $bind 要绑定的参数列表
     * @return void
     * @throws \think\Exception
     */
    protected function bindValue(array $bind = [])
    {
        foreach ($bind as $key => $val) {
            // 占位符
            $param = is_numeric($key) ? $key + 1 : ':' . $key;
            if (is_array($val)) {
                $result = $this->PDOStatement->bindValue($param, $val[0], $val[1]);
            } else {
                $result = $this->PDOStatement->bindValue($param, $val);
            }
            if (!$result) {
                throw new Exception(
                    "Error occurred  when binding parameters '{$param}'" . (string)$this->config . (string)$this->queryStr . (string)$bind
                );
            }
        }
    }

    /**
     * 根据参数绑定组装最终的SQL语句 便于调试
     * @access public
     * @param string $sql 带参数绑定的sql语句
     * @param array $bind 参数绑定列表
     * @return string
     */
    protected function getBindSql($sql, array $bind = [])
    {
        if ($bind) {
            foreach ($bind as $key => $val) {
                $val = $this->quote(is_array($val) ? $val[0] : $val);

                $sql = is_numeric($key) ?
                substr_replace($sql, $val, strpos($sql, '?'), 1) :
                str_replace([':' . $key . ')', ':' . $key . ' '], [$val . ')', $val . ' '], $sql . ' ');
            }
        }
        return $sql;
    }

    /**
     * 获得数据集
     * @access protected
     * @return array
     */
    protected function getResult()
    {
        $result        = $this->PDOStatement->fetchAll($this->fetchType);
        $this->numRows = count($result);
        return $result;
    }

    public function startTrans()
    {
        $this->initConnect();
        $this->link->beginTransaction();
        return;
    }

    public function commit()
    {
    	try {
            $this->link->commit();
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit;
        }
        return true;
    }

    public function rollback()
    {
    	try {
            $this->link->rollback();
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function quote($str)
    {
        return $this->link ? $this->link->quote($str) : $str;
    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free()
    {
        $this->PDOStatement = null;
    }

    /**
     * 数据库调试 记录当前SQL
     * @access protected
     * @param boolean $start  调试开始标记 true 开始 false 结束
     */
    protected function debug($start)
    {
        // if (!empty($this->config['debug'])) {
        //     // 开启数据库调试模式
        //     if ($start) {
        //         Debug::remark('queryStartTime', 'time');
        //     } else {
        //         // 记录操作结束时间
        //         Debug::remark('queryEndTime', 'time');
        //         $runtime = Debug::getRangeTime('queryStartTime', 'queryEndTime');
        //         $log     = $this->queryStr . ' [ RunTime:' . $runtime . 's ]';
        //         $result  = [];
        //         // SQL性能分析
        //         if (0 === stripos(trim($this->queryStr), 'select')) {
        //             $result = $this->getExplain($this->queryStr);
        //         }
        //         // SQL监听
        //         // $this->trigger($this->queryStr, $runtime, $result);
        //     }
        // }
    }

    /**
     * 调用Query类的查询方法
     * @param  [type] $method [description]
     * @param  [type] $param  [description]
     * @return [type]         [description]
     */
    public function __call($method, $param)
    {
    	return call_user_func_array([$this->query, $method], $param);
    }

    /**
     * 解析pdo连接的dsn信息（由驱动扩展）
     * @access public
     * @param array $config 连接信息
     * @return string
     */
    abstract protected function parseDsn($config);
}