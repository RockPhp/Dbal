<?php

class Rock_DbAl_Pdo_Stmt implements Rock_DbAl_Iface_IStmt
{

    protected $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function nextObject($upperCase = false)
    {
        return $this->stmt->fetchObject();
    }

    public function nextArray($upperCase = false)
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function nextArrayInt()
    {
        return $this->stmt->fetch(PDO::FETCH_NUM);
    }

    public function resetCursor()
    {
        // implementar...
        // $this->stmt->fetch(null, 0);
    }

    public function closeCursor()
    {
        $this->stmt->closeCursor();
    }

    public function numCollumns()
    {
        return $this->stmt->columnCount();
    }

    public function numRows()
    {
        // nao funciona no pdo, implementar
        return $this->stmt->rowCount();
    }
}
