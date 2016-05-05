<?php
namespace Core\Db;

use Core\Db;

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
            $class            = __NAMESPACE__ . '\\Builder\\' .ucfirst($driver);
            $builder[$driver] = new $class($this->connection);
        }
        // 设置当前查询对象
        $builder[$driver]->setQuery($this);
        return $builder[$driver];
    }
}