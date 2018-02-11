<?php
class db
{
    private $dbh;

    function __construct($dbhost, $dbuser, $dbpw, $dbname = '', $port = 3306) {
        try {
            $this->dbh = new PDO("mysql:dbname=$dbname;host=$dbhost;port=$port", $dbuser, $dbpw, array(PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8", PDO::ATTR_CASE => PDO::CASE_LOWER));
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }



    function fetch_total($table, $where = '1') {
        return $this->query("SELECT COUNT(*) FROM " . DB_TABLEPRE . "$table WHERE $where")->fetchColumn(0);
    }


    /**
     * 执行一条sql语句
     * @param type $sql
     * @return int
     */
    function exec($sql) {
        return $this->dbh->exec($sql);
    }

    function query($sql) {
        return $this->dbh->query($sql);
    }
/**
    * 作用:获取單行数据
    * 返回:表內第一条記录
    * 类型:数组
    * 參数:select * from table where id='1'
    */
    public function getOne($sql) {
      return self::_fetch ( $sql, $type = '0' );
    }

    //开始事务
    function begin_transaction() {
        $this->dbh->beginTransaction();
    }


    //提交事务
    function commit_transaction() {
        $this->dbh->commit();
    }

    //事务回滚
    function rollback_transaction() {
        $this->dbh->rollBack();
    }

    function num_rows($query) {
        $query = mysql_num_rows($query);
        return $query;
    }

    function insert_id($table, $field) {
        $query = $this->query("SELECT * FROM " . DB_TABLEPRE . "$table WHERE 1=1");
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row[$field];
    }

    function version() {
        return $this->dbh->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
    }

    function get_pid() {
        return substr(md5(time() . microtime() . rand(0, 10000000)), 0, 32);
    }

}

