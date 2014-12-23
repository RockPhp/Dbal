<?php

class Rock_DbAl_Conn
{

    protected $connection;
    // dsn jdbc:
    // http://pic.dhe.ibm.com/infocenter/cbi/v10r1m1/index.jsp?topic=%2Fcom.ibm.swg.ba.cognos.vvm_ag_guide.10.1.1.doc%2Fc_ag_samjdcurlform.html
    //
    public function __construct($dsn, $user = null, $passwd = null)
    {
        $arrayConn = explode(':', $dsn);
        $dbDrive = trim($arrayConn[1]);
        $dbDrive = preg_replace('/[^A-z0-9]/', '', $dbDrive);
        $dbDrive = Rock_DbAl_Map::getConnClass($dbDrive);
        $connection = new $dbDrive();
        $this->setConnection($connection);
        $this->connection->connect($dsn, $user, $passwd);
    }

    private function setConnection(Rock_DbAl_Iface_IConn $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
