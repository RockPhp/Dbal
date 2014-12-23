<?php

class Rock_DbAl_Pgsql_Stmt implements Rock_DbAl_Iface_IStmt
{

    protected $stmt;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    public function nextObject($upperCase = false)
    {
        return pg_fetch_object($this->stmt);
    }

    public function nextArray($upperCase = false)
    {
        return pg_fetch_array($this->stmt, null, PGSQL_ASSOC);
    }

    public function nextArrayInt()
    {
        return pg_fetch_array($this->stmt, null, PGSQL_NUM);
    }

    public function resetCursor()
    {
        pg_result_seek($this->stmt, 0);
    }

    public function closeCursor()
    {
        pg_free_result($this->stmt);
    }

    public function numCollumns()
    {
        return pg_num_fields($this->stmt);
    }

    public function numRows()
    {
        return pg_num_rows($this->stmt);
    }
}
