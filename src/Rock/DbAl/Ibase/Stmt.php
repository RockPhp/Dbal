<?php

class Rock_DbAl_Ibase_Stmt implements Rock_DbAl_Iface_IStmt
{

    protected $stmt;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    public function nextObject($upperCase = false)
    {
        return ibase_fetch_object($this->stmt);
    }

    public function nextArray($upperCase = false)
    {
        return ibase_fetch_assoc($this->stmt);
    }

    public function nextArrayInt()
    {
        return ibase_fetch_row($this->stmt);
    }

    public function resetCursor()
    {}

    public function closeCursor()
    {
        ibase_free_result($this->stmt);
    }

    public function numCollumns()
    {
        return ibase_num_fields($this->stmt);
    }

    public function numRows()
    {
        return ibase_affected_rows($this->stmt);
    }
}
