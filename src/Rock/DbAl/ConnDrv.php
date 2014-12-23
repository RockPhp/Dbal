<?php

class Rock_DbAl_ConnDrv
{

    protected $connection;

    public static function errorHandler($errorNum, $errorMsg)
    {
        throw new Exception($errorNum . ' ' . $errorMsg);
    }

    protected function checkBind($sql, $arrayBind)
    {
        $sqlarr = explode('?', trim($sql));
        if (count($arrayBind) !== (count($sqlarr)) - 1) {
            throw new Exception('Quantidade de Binds na query diferente da ArrayBind');
        }
        return $sqlarr;
    }
}
