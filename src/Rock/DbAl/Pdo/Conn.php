<?php

class Rock_DbAl_Pdo_Conn extends Rock_DbAl_ConnDrv
{

    protected $autoCommit = true;

    protected $openedTrans = false;

    protected function beginTransaction()
    {
        if (! $this->openedTrans) {
            $this->connection->beginTransaction();
            $this->openedTrans = true;
        }
    }

    protected function commit()
    {
        if ($this->openedTrans) {
            $this->connection->commit();
            $this->openedTrans = false;
        }
    }

    protected function connect($dsn, $user = null, $passwd = null)
    {
        $this->connection = new PDO($dsn, $user, $passwd);
    }

    protected function disconnect()
    {
        $this->connection = null;
    }

    protected function getErrorCode()
    {
        return $this->connection->errorCode();
    }

    protected function getErrorMsg()
    {
        $arrayError = $this->connection->errorInfo();
        return implode(' ', $arrayError);
    }

    protected function rollBack()
    {
        if ($this->openedTrans) {
            $this->connection->rollBack();
            $this->openedTrans = false;
        }
    }

    protected function runQuery($sql, array $arrayBind = array())
    {
        if (! $this->autoCommit) {
            $this->beginTransaction();
        }
        $rs = $this->connection->prepare($sql);
        $this->checkErrors($rs);
        $rs->execute($arrayBind);
        $this->checkErrors($rs);
        
        if (! $this->openedTrans && $this->autoCommit) {
            $this->commit();
        }
        return $rs;
    }

    protected function checkErrors($rs)
    {
        if ($rs instanceof PDOStatement) {
            $errorCode = $rs->errorCode();
            if (! empty($errorCode) && $errorCode != '00000') {
                throw new Exception('Erro query ' . $this->getErrorMsg());
            }
        } else if ($rs === false) {
            throw new Exception('Erro query ' . $this->getErrorMsg());
        }
    }

    protected function setAutoCommit($autocommit = true)
    {
        $this->autoCommit = $autocommit;
    }

    protected function insertId()
    {
        return $this->connection->lastInsertId();
    }

    protected function getNewBind($value = null, $maxLenght = null, $type = null)
    {
        return new Rock_DbAl_Pdo_Bind($value, $maxLenght, $type);
    }
}
