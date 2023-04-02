<?php

class Rock_DbAl_Pdo_Stmt implements Rock_DbAl_Iface_IStmt
{

    /**
     *
     * @var PDO
     */
    protected $conn;

    /**
     *
     * @var PDOStatement
     */
    protected $stmt;

    /**
     *
     * @var string
     */
    protected $query;

    /**
     *
     * @var array
     */
    protected $arrayBind;

    public function __construct(PDOStatement $stmt, PDO $conn = null, $query = null, $arrayBind = array())
    {
        $this->stmt = $stmt;
        $this->conn = $conn;
        $this->query = $query;
        $this->arrayBind = $arrayBind;
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
        return $this->stmt->rowCount();
    }
}
