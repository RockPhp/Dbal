<?php

class Rock_DbAl_Oci8_Stmt implements Rock_DbAl_Iface_IStmt
{

    protected $stmt;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    public function nextObject($upperCase = false)
    {
        $obj = oci_fetch_object($this->stmt);
        if (isset($obj->DBAL_TMP_ROWNUM)) {
            unset($obj->DBAL_TMP_ROWNUM);
        }
        return $obj;
    }

    public function nextArray($upperCase = false)
    {
        $array = oci_fetch_array($this->stmt, OCI_ASSOC + OCI_RETURN_NULLS);
        if (isset($array['DBAL_TMP_ROWNUM'])) {
            unset($array['DBAL_TMP_ROWNUM']);
        }
        return $array;
    }

    public function nextArrayInt()
    {
        $array = oci_fetch_array($this->stmt, OCI_NUM + OCI_RETURN_NULLS);
        return $array;
    }

    public function resetCursor()
    {}

    public function closeCursor()
    {
        oci_free_statement($this->stmt);
    }

    public function numCollumns()
    {
        return oci_num_fields($this->stmt);
    }

    public function numRows()
    {
        // nao funciona pra select, implementar
        return oci_num_rows($this->stmt);
    }
}
