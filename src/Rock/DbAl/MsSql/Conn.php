<?php

class Rock_DbAl_MsSql_Conn extends Rock_DbAl_ConnDrv implements Rock_DbAl_Iface_IConn
{

    private $autoCommit = true;

    private $openedTrans = false;

    public function beginTransaction()
    {
        if (! $this->openedTrans) {
            mssql_query("BEGIN TRANSACTION", $this->connection);
            $this->openedTrans = true;
        }
    }

    public function commit()
    {
        if ($this->openedTrans) {
            mssql_query("COMMIT TRANSACTION", $this->connection);
            $this->openedTrans = false;
        }
    }

    public function connect($dsn, $user = null, $passwd = null)
    {
        $arrayConn = array();
        $arrayConn = explode(':', $dsn);
        unset($arrayConn[0]);
        unset($arrayConn[1]);
        $dsn = implode(':', $arrayConn);
        $arrayConn = array();
        $parseUrl = parse_url($dsn);
        $arrayConn['dbHost'] = $parseUrl['host'];
        $arrayConn['dbName'] = str_replace('/', '', $parseUrl['path']);
        $arrayConn['dbPort'] = $parseUrl['port'];
        if (! empty($arrayConn['dbPort'])) {
            $arrayConn['dbHost'] .= ':' . $arrayConn['dbPort'];
        }
        set_error_handler(array(
            'Rock_DbAl_ConnDrv',
            'errorHandler'
        ));
        $this->connection = mssql_connect($arrayConn['dbHost'], $user, $passwd, true);
        mssql_select_db($arrayConn['dbName'], $this->connection);
        restore_error_handler();
    }

    public function disconnect()
    {
        mssql_close($this->connection);
    }

    public function getErrorCode()
    {
        // TODO: Auto-generated method stub
    }

    public function getErrorMsg()
    {
        return mssql_get_last_message();
    }

    public function rollBack()
    {
        if ($this->openedTrans) {
            mssql_query("ROLLBACK TRANSACTION", $this->connection);
            $this->openedTrans = false;
        }
    }

    private function addLimit($sql, $start = null, $limit = null)
    {
        $top = $start + $limit;
        if ($top > 0) {
            $sql = preg_replace('/(^\s*select\s+(distinctrow|distinct)?)/i', '\\1 TOP ' . $top . " ", $sql);
        }
        return $sql;
    }

    private function addQueryDecl($pos, $value)
    {
        $type = 'CHAR';
        if (is_string($value)) {
            $len = strlen($value);
            if ($len > 4000) {
                $type = "NTEXT";
            } else {
                $len = ($len == 0) ? 1 : $len;
                $type = "NVARCHAR($len)";
            }
        } else if (is_integer($value) || is_bool($value)) {
            $type = "INT";
        } else if (is_float($value)) {
            $type = "FLOAT";
        }
        $str = '@P' . $pos . ' ' . $type;
        return $str;
    }

    private function addQueryValue($pos, $value)
    {
        if (is_string($value)) {
            $value = "N'" . str_replace("'", "''", $value) . "'";
        } else if ($value === false) {
            $value = '0';
        } else if ($value === null) {
            $value = 'NULL';
        }
        $str = '@P' . $pos . "=" . $value;
        return $str;
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
            $sql = "sp_executesql \nN'";
            $arrDecl = array();
            $arrValue = array();
            for ($i = 0; $i < (count($sqlarr) - 1); $i ++) {
                $sql .= $sqlarr[$i] . '@P' . $i;
                $arrDecl[] = $this->addQueryDecl($i, $arrayBind[$i]);
                $arrValue[] .= $this->addQueryValue($i, $arrayBind[$i]);
            }
            $sql .= "',\nN'";
            $sql .= implode(", ", $arrDecl) . "', \n";
            $sql .= implode(", ", $arrValue);
        }
        if (! $this->autoCommit) {
            $this->beginTransaction();
        }
        $rs = mssql_query($sql, $this->connection);
        if (! $this->openedTrans && $this->autoCommit) {
            $this->commit();
        }
        if (! is_bool($rs)) {
            mssql_data_seek($rs, $start);
        }
        restore_error_handler();
        $stmt = new Rock_DbAl_MsSql_Stmt($rs);
        return $stmt;
    }

    public function setAutoCommit($autocommit = true)
    {
        $this->autoCommit = $autocommit;
    }

    public function insertId()
    {
        // TODO: Auto-generated method stub
    }

    public function getNewBind($value = null, $maxLenght = null, $type = null)
    {
        return new Rock_DbAl_MsSql_Bind($value, $maxLenght, $type);
    }
}
