<?php

class Rock_DbAl_PdoFb_Conn extends Rock_DbAl_Pdo_Conn implements Rock_DbAl_Iface_IConn
{
    // firebird:dbname=localhost:/var/lib/firebird/2.5/data/employee.fdb
    public function connect($dsn, $user = null, $passwd = null)
    {
        $parseUrl = parse_url($dsn);
        $pdoDsn = $parseUrl['path'];
        parent::connect($pdoDsn, $user, $passwd);
    }

    public function disconnect()
    {
        parent::disconnect();
    }

    private function addLimit($sql, $start = null, $limit = null)
    {
        $limitStr = '';
        if (! empty($limit)) {
            $limitStr .= " \n FIRST " . $limit;
        }
        if (! empty($start)) {
            $limitStr .= " \n SKIP " . $start;
        }
        if (! empty($limitStr)) {
            $limitStr = "SELECT \n " . $limitStr . " \n ";
            $sql = preg_replace('/^[ \t]*select/i', $limitStr, $sql);
        }
        return $sql;
    }

    public function runQuery($sql, array $arrayBind = array(), $start = null, $limit = null)
    {
        $sql = $this->addLimit($sql, $start, $limit);
        $stmt = parent::runQuery($sql, $arrayBind);
        return new Rock_DbAl_PdoFb_Stmt($stmt);
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
