<?php
namespace Core\Db;

use Core\Db;
use Core\Exception;

class Query
{
	// 数据库Connection对象实例
    protected $connection;
    // 数据库驱动类型
    protected $driver;

    // 查询参数
    protected $options = [];
    // 参数绑定
    protected $bind = [];

    public function __construct($connection = '')
    {
        $this->connection = $connection ?: Db::connect();
        $this->driver     = $this->connection->getDriverName();
    }

    /**
     * 获取当前的builder实例对象
     * @access protected
     * @return \Core\Db\Builder
     */
    protected function builder()
    {
        static $builder = [];
        $driver         = $this->driver;
        if (!isset($builder[$driver])) {
            $class            = __NAMESPACE__ . '\\Builder\\' . ucfirst(strtolower($driver));
            $builder[$driver] = new $class($this->connection);
        }
        // 设置当前查询对象
        $builder[$driver]->setQuery($this);
        return $builder[$driver];
    }

    public function table($tables)
    {
        if(empty($tables)) {
            throw new Exception('table name can\'t empty');
        }
        $this->options['table'] = $tables;
        return $this;
    }

    /**
     * sql join
     * @param  [mixed] $joins [description]
     * @param  string  $type  INNER JOIN,LEFT JOIN,RIGHT JOIN,FULL JOIN
     * @return [type]         [description]
     */
    public function join($join, $on, $type = 'INNER JOIN')
    {
        if(empty($join)) {
            throw new Exception('join name can\'t empty');
        }
        $this->options['join'][] = [
            'join' => $join,
            'on'   => $on,
            'type' => $type
        ];
        return $this;
    }

    public function group($group)
    {
        if(is_array($group)) {
            $this->options['group'] = $this->options['group'] + $group;
        }
        if(!empty($group) && is_string($group)) {
            $this->options['group'][] = $group;
        }
        return $this;
    }

    public function field($fields)
    {
        $this->options['field'] = $fields;
        return $this;
    }

    public function select()
    {
        $sql = $this->builder()->select($this->options);
        if(isset($this->options['get_sql']) && $this->options['get_sql'] == true) {
            $this->resetOptions();
            return $sql;
        }
        $this->resetOptions();
        return $this->connection->query($sql);
    }

    public function update()
    {
        if(empty($this->options['where'])) {
            throw new Exception(" need where condition!");
        }
        $sql = $this->builder()->update($this->options);
        if(isset($this->options['get_sql']) && $this->options['get_sql'] == true) {
            $this->resetOptions();
            return $sql;
        }
        $this->resetOptions();
        return $this->connection->execute($sql);
    }

    public function delete()
    {
        if(empty($this->options['where'])) {
            throw new Exception(" need where condition!");
        }
        $sql = $this->builder()->delete($this->options);
        if(isset($this->options['get_sql']) && $this->options['get_sql'] == true) {
            $this->resetOptions();
            return $sql;
        }
        $this->resetOptions();
        return $this->connection->execute($sql);
    }

    public function insert()
    {
        if(empty($this->options['data'])) {
            throw new Exception(" need data condition!");
        }
        $sql = $this->builder()->insert($this->options);
        if(isset($this->options['get_sql']) && $this->options['get_sql'] == true) {
            $this->resetOptions();
            return $sql;
        }
        $this->resetOptions();
        return $this->connection->execute($sql);
    }

    public function resetOptions()
    {
        $this->options = [];
    }

    public function data($data, $multi = false)
    {
        if($multi) {
            foreach ($data as  $value) {
                $this->data((array)$value);
            }
        } else {
            $this->options['data'][] = (array)$data;
        }
        return $this;
    }

    public function limit()
    {
        $this->options['limit'] = implode(',', array_slice(func_get_args(), 0, 2));
        return $this;
    }

    public function order($order)
    {
        if(is_array($order)) {
            $this->options['order'] = $this->options['order'] + $order;
        }
        if(!empty($order) && is_string($order)) {
            $this->options['order'][] = $order;
        }
        return $this;
    }


    public function where()
    {
        $args = func_get_args();
        switch (func_num_args()) {
            case 1:
                $args = $args[0];
                    if(is_array($args)) {
                        foreach ($args as $key => $value) {
                            $this->where($value);
                        }
                        return $this;
                    }
                break;
            case 2:
                list($key, $condition) = $args;
                $this->options['where'][] = array($key, '=' , $condition);
                return $this;
            case 3:
                list($key, $compare, $condition) = $args;
                $this->options['where'][] = array($key, $compare , $condition);
                return $this;
        }
        throw new Exception("param num error");
    }

    public function getSql($flag = false)
    {
        $this->options['get_sql'] = $flag;
        return $this;
    }
}