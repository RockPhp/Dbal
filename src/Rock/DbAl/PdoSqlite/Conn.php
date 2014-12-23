<?php

class Rock_DbAl_PdoSqlite_Conn extends Rock_DbAl_Pdo_Conn implements Rock_DbAl_Iface_IConn
{

    public function connect($dsn, $user = null, $passwd = null)
    {
        $parseUrl = parse_url($dsn);
        $parseUrl = parse_url($parseUrl['path']);
        $pdoDsn = 'sqlite:' . trim($parseUrl['path']);
        parent::connect($pdoDsn);
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
        return new Rock_DbAl_PdoSqlite_Stmt($stmt);
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

    public function getNewBind($value = null, $maxLenght = null, $type = null)
    {
        return parent::getNewBind($value, $maxLenght, $type);
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
