<?php

class Rock_DbAl_Oci8_Conn extends Rock_DbAl_ConnDrv implements Rock_DbAl_Iface_IConn
{

    private $commitMode = OCI_COMMIT_ON_SUCCESS;

    private $commitModeTmp = OCI_COMMIT_ON_SUCCESS;

    private $rs = null;

    public function beginTransaction()
    {
        $this->commitModeTmp = $this->commitMode;
        $this->commitMode = OCI_DEFAULT;
    }

    public function commit()
    {
        oci_commit($this->connection);
        $this->commitMode = $this->commitModeTmp;
    }

    private function parseThin($arrayConf)
    {
        $strConn = '';
        $arrayConn = array();
        $arrayConn['dbHost'] = str_replace('@', '', $arrayConf[3]);
        $arrayConn['dbName'] = trim($arrayConf[5]);
        $arrayConn['dbPort'] = trim($arrayConf[4]);
        $strConn = '//' . $arrayConn['dbHost'];
        if (! empty($arrayConn['dbPort'])) {
            $strConn .= ':' . $arrayConn['dbPort'];
        }
        $strConn .= '/' . $arrayConn['dbName'];
        return $strConn;
    }

    private function parseOci($arrayConf)
    {
        $strConn = substr($arrayConf[3], 1);
        return $strConn;
    }

    public function disconnect()
    {
        set_error_handler(array(
            'Rock_DbAl_ConnDrv',
            'errorHandler'
        ));
        oci_close($this->connection);
        restore_error_handler();
    }

    private function getStrConn($dsn)
    {
        $arrayConf = explode(':', $dsn);
        $strConn = $this->parseOci($arrayConf);
        if ($arrayConf[2] == 'thin') {
            $strConn = $this->parseThin($arrayConf);
        }
        return $strConn;
    }

    public function connect($dsn, $user = null, $passwd = null)
    {
        $strConn = $this->getStrConn($dsn);
        set_error_handler(array(
            'Rock_DbAl_ConnDrv',
            'errorHandler'
        ));
        $this->connection = oci_new_connect($user, $passwd, $strConn);
        restore_error_handler();
    }

    public function getErrorCode()
    {
        $arrayError = oci_error($this->connection);
        return $arrayError['code'];
    }

    public function getErrorMsg()
    {
        $arrayError = oci_error($this->connection);
        if ($arrayError === false && $this->rs !== null) {
            $arrayError = oci_error($this->rs);
        }
        $strError = '[' . $arrayError['code'] . '] ';
        $strError .= '[' . $arrayError['message'] . '] ';
        $strError .= '[' . $arrayError['offset'] . '] ';
        $strError .= '[' . $arrayError['sqltext'] . ']';
        return $strError;
    }

    public function rollBack()
    {
        oci_rollback($this->connection);
        $this->commitMode = $this->commitModeTmp;
    }

    private function addLimit($sql, $start = null, $limit = null)
    {
        if (! empty($start) || ! empty($limit)) {
            $start = empty($start) ? 0 : $start;
            $sql = 'SELECT *
                FROM (SELECT ROWNUM as DBAL_TMP_ROWNUM,a_tmp_row_num.*
                FROM (' . $sql . ') a_tmp_row_num)
                WHERE dbal_tmp_rownum > ' . $start;
            if (! empty($limit)) {
                $sql .= ' AND ROWNUM <= ' . $limit;
            }
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
        $binds = array();
        $sqlarr = $this->checkBind($sql, $arrayBind);
        if (count($arrayBind) > 0) {
            $sql = '';
            for ($i = 0; $i < (count($sqlarr) - 1); $i ++) {
                $sql .= $sqlarr[$i] . ':' . $i;
            }
            $sql .= $sqlarr[$i ++];
            $rs = oci_parse($this->connection, $sql);
            foreach ($arrayBind as $k => $v) {
                if ($v instanceof Rock_DbAl_Iface_IBind) {
                    $binds[$k] = $v->getValue();
                    oci_bind_by_name($rs, ':' . $k, $binds[$k], $v->getMaxLenght(), $v->getType());
                } else {
                    oci_bind_by_name($rs, ':' . $k, $arrayBind[$k]);
                }
            }
        } else {
            $rs = oci_parse($this->connection, $sql);
        }
        $this->rs = $rs;
        oci_execute($rs, $this->commitMode);
        foreach ($binds as $k => $v) {
            $arrayBind[$k]->setValue($v);
        }
        restore_error_handler();
        
        $stmt = new Rock_DbAl_Oci8_Stmt($rs);
        return $stmt;
    }

    public function setAutoCommit($autocommit = true)
    {
        if ($autocommit) {
            $this->commitMode = OCI_COMMIT_ON_SUCCESS;
        } else {
            $this->commitMode = OCI_DEFAULT;
        }
    }

    public function insertId()
    {
        // TODO: Auto-generated method stub
    }

    public function getNewBind($value = null, $maxLenght = null, $type = null)
    {
        return new Rock_DbAl_Oci8_Bind($value, $maxLenght, $type);
    }
}

