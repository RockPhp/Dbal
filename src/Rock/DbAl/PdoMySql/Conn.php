<?php

class Rock_DbAl_PdoMySql_Conn extends Rock_DbAl_Pdo_Conn implements Rock_DbAl_Iface_IConn
{
    // mysql:host=localhost;port=3307;dbname=testdb
    public function connect($dsn, $user = null, $passwd = null)
    {
        $arrayConn = array();
        $parseUrl = parse_url($dsn);
        $parseUrl = parse_url($parseUrl['path']);
        $arrayConn['host'] = $parseUrl['host'];
        $arrayConn['dbname'] = preg_replace("/[^A-z0-9_]/", '', $parseUrl['path']);
        $pdoDsn = 'mysql:host=' . $arrayConn['host'];
        $pdoDsn .= ';dbname=' . $arrayConn['dbname'];
        parent::connect($pdoDsn, $user, $passwd);
    }

    public function disconnect()
    {
        parent::disconnect();
    }

    private function addLimit($sql, $start = null, $limit = null)
    {
        if (! empty($start) || ! empty($limit)) {
            $start = empty($start) ? 0 : $start;
            $sql .= ' LIMIT ' . $start . ',' . $limit;
        }
        return $sql;
    }

    public function runQuery($sql, array $arrayBind = array(), $start = null, $limit = null)
    {
        $sql = $this->addLimit($sql, $start, $limit);
        $stmt = parent::runQuery($sql, $arrayBind);
        return new Rock_DbAl_PdoMySql_Stmt($stmt);
    }

    public function getNewBind($value = null, $maxLenght = null, $type = null)
    {
        return parent::getNewBind($value, $maxLenght, $type);
    }

    public function setAutoCommit($autocommit = true)
    {
        parent::setAutoCommit($autocommit);
    }

    public function beginTransaction()
    {
        parent::beginTransaction();
    }

    public function commit()
    {
        parent::commit();
    }

    public function rollBack()
    {
        parent::rollBack();
    }

    public function getErrorMsg()
    {
        return parent::getErrorMsg();
    }

    public function getErrorCode()
    {
        return parent::getErrorCode();
    }

    public function insertId()
    {
        return parent::insertId();
    }
}
