<?php

class Rock_DbAl_Pgsql_Conn extends Rock_DbAl_ConnDrv implements Rock_DbAl_Iface_IConn
{

    private $autoCommit = true;

    private $openedTrans = false;

    public function beginTransaction()
    {
        if (! $this->openedTrans) {
            pg_query($this->connection, "BEGIN TRANSACTION");
            $this->openedTrans = true;
        }
    }

    public function commit()
    {
        if ($this->openedTrans) {
            pg_query($this->connection, "COMMIT");
            $this->openedTrans = false;
        }
    }

    public function connect($dsn, $user = null, $passwd = null)
    {
        $arrayConn = array();
        $parseUrl = parse_url($dsn);
        $parseUrl = parse_url($parseUrl['path']);
        $arrayConn['dbHost'] = $parseUrl['host'];
        $arrayConn['dbName'] = str_replace('/', '', $parseUrl['path']);
        $arrayConn['dbPort'] = $parseUrl['port'];
        $strConn = 'host=' . $arrayConn['dbHost'];
        if (! empty($arrayConn['dbPort'])) {
            $strConn .= ' port=' . $arrayConn['dbPort'];
        }
        $strConn .= ' dbname=' . $arrayConn['dbName'];
        $strConn .= ' user=' . $user;
        if (! empty($passwd)) {
            $strConn .= ' password=' . $passwd;
        }
        set_error_handler(array(
            'Rock_DbAl_ConnDrv',
            'errorHandler'
        ));
        $this->connection = pg_connect($strConn);
        restore_error_handler();
    }

    public function disconnect()
    {
        pg_close($this->connection);
    }

    public function getErrorCode()
    {
        // TODO: Auto-generated method stub
    }

    public function getErrorMsg()
    {
        return pg_last_error($this->connection);
    }

    public function rollBack()
    {
        if ($this->openedTrans) {
            pg_query($this->connection, "ROLLBACK");
            $this->openedTrans = false;
        }
    }

    private function addLimit($sql, $start = null, $limit = null)
    {
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (integer) $limit;
        }
        if ($start !== null) {
            $sql .= ' OFFSET ' . (integer) $start;
        }
        return $sql;
    }

    public function runQuery($sql, array $arrayBind = array(), $start = null, $limit = null)
    {
        $sql = $this->addLimit($sql, $start, $limit);
        set_error_handler(array(
            'Rock_DbAl_ConnDrv',
            'errorHandler'
        ));
        $sqlarr = $this->checkBind($sql, $arrayBind);
        
        if (! $this->autoCommit) {
            $this->beginTransaction();
        }
        if (count($arrayBind) > 0) {
            $sql = '';
            for ($i = 0; $i < (count($sqlarr) - 1); $i ++) {
                $sql .= $sqlarr[$i] . '$' . ($i + 1);
            }
            $sql .= $sqlarr[$i ++];
            $rs = pg_query_params($this->connection, $sql, $arrayBind);
        } else {
            $rs = pg_query($this->connection, $sql);
        }
        if (! $this->openedTrans && $this->autoCommit) {
            $this->commit();
        }
        restore_error_handler();
        $stmt = new Rock_DbAl_Pgsql_Stmt($rs);
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
        return new Rock_DbAl_Pgsql_Bind($value, $maxLenght, $type);
    }
}
