<?php

class Rock_DbAl_PdoSqlite_Stmt extends Rock_DbAl_Pdo_Stmt
{

    public function numRows()
    {
        $sql = $this->getQueryCount();
        $rs = $this->conn->prepare($sql);
        $rs->execute($this->arrayBind);
        $total = $rs->fetchColumn(0);
        return $total;
    }

    public function getQueryCount()
    {
        $sql = 'SELECT COUNT(1) FROM (' . $this->query . ')';
        return $sql;
    }
}
