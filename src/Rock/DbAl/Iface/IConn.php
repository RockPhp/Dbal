<?php

interface Rock_DbAl_Iface_IConn
{

    public function connect($dsn, $user = null, $passwd = null);
    
    public function disconnect();

    public function setAutoCommit($autocommit = true);

    public function beginTransaction();

    public function commit();

    public function rollBack();

    /**
     *
     * @param string $value            
     * @param string $maxLenght            
     * @param string $type            
     * @return Rock_DbAl_Iface_IBind
     */
    public function getNewBind($value = null, $maxLenght = null, $type = null);

    /**
     *
     * @param string $sql            
     * @param array $arrayBind            
     * @param string $start            
     * @param string $limit            
     * @return Rock_DbAl_Iface_IStmt
     */
    public function runQuery($sql, array $arrayBind = array(), $start = null, $limit = null);

    public function getErrorMsg();

    public function getErrorCode();

    public function insertId();
}
