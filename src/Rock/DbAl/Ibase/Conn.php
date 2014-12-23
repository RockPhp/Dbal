<?php

class Rock_DbAl_Ibase_Conn extends Rock_DbAl_ConnDrv implements Rock_DbAl_Iface_IConn
{

    public function beginTransaction()
    {}

    public function commit()
    {}
    
    // ibase:localhost:/var/lib/firebird/2.5/data/employee.fdb
    public function connect($dsn, $user = null, $passwd = null)
    {
        $arrayConn = explode(':', $dsn);
        unset($arrayConn[0]);
        unset($arrayConn[1]);
        $dsn = implode(':', $arrayConn);
        set_error_handler(array(
            'Rock_DbAl_ConnDrv',
            'errorHandler'
        ));
        $this->connection = ibase_connect($dsn, $user, $passwd);
        restore_error_handler();
    }

    public function disconnect()
    {
        ibase_close($this->connection);
    }

    public function getErrorCode()
    {
        return ibase_errcode();
    }

    public function getErrorMsg()
    {
        return ibase_errmsg();
    }

    public function rollBack()
    {}

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

    private function fixBindValue($value)
    {
        if (is_string($value)) {
            $value = "'" . str_replace("'", "''", $value) . "'";
        } else if ($value === false) {
            $value = '0';
        } else if ($value === null) {
            $value = 'NULL';
        }
        return $value;
    }

    public function runQuery($sql, array $arrayBind = array(), $start = null, $limit = null)
    {
        $sql = $this->addLimit($sql, $start, $limit);
        set_error_handler(array(
            'Rock_DbAl_ConnDrv',
            'errorHandler'
        ));
        $sqlarr = $this->checkBind($sql, $arrayBind);
        if (count($arrayBind) > 0) {
            $sql = '';
            for ($i = 0; $i < (count($sqlarr) - 1); $i ++) {
                $sql .= $sqlarr[$i] . $this->fixBindValue($arrayBind[$i]);
            }
        }
        $rs = ibase_query($this->connection, $sql);
        restore_error_handler();
        $stmt = new Rock_DbAl_Ibase_Stmt($rs);
        return $stmt;
    }

    public function setAutoCommit($autocommit = true)
    {}

    public function insertId()
    {}

    public function getNewBind($value = null, $maxLenght = null, $type = null)
    {
        return new Rock_DbAl_Ibase_Bind($value, $maxLenght, $type);
    }
}
