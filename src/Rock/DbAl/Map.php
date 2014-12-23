<?php

class Rock_DbAl_Map
{

    public static function getConnClass($drvstr)
    {
        $arrayMap = array(
            'Pgsql' => 'pgsql',
            'Oci8' => 'oracle',
            'PdoSqlite' => 'sqlite',
            'PdoMySql' => 'mysql',
            'MsSql' => 'microsoft',
            'PdoFb' => 'firebird',
            'Ibase' => 'ibase'
        );
        $strClass = 'Rock_DbAl_' . array_search($drvstr, $arrayMap) . '_Conn';
        return $strClass;
    }
}
