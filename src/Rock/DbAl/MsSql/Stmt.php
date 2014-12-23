<?php

class Rock_DbAl_MsSql_Stmt implements Rock_DbAl_Iface_IStmt
{

    protected $stmt;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    public function nextObject($upperCase = false)
    {
        return mssql_fetch_object($this->stmt);
    }

    public function nextArray($upperCase = false)
    {
        return mssql_fetch_array($this->stmt, MSSQL_ASSOC);
    }

    public function nextArrayInt()
    {
        return mssql_fetch_array($this->stmt, MSSQL_NUM);
    }

    public function resetCursor()
    {
        mssql_data_seek($this->stmt, 0);
    }

    public function closeCursor()
    {
        mssql_free_result($this->stmt);
    }

    public function numCollumns()
    {
        return mssql_num_fields($this->stmt);
    }

    public function numRows()
    {
        return mssql_num_rows($this->stmt);
    }
}
