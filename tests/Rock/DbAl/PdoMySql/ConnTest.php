<?php

final class ConnTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @test
     */
    public function dsn()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $pdoDsn = $mysqlConn->getMysqlPdoDsn("jdbc:mysql://localhost/testdb");
        $this->assertEquals("mysql:host=localhost;port=3306;dbname=testdb", $pdoDsn);

        $pdoDsn = $mysqlConn->getMysqlPdoDsn("jdbc:mysql://localhost/testdb?invalid=thing");
        $this->assertEquals("mysql:host=localhost;port=3306;dbname=testdb", $pdoDsn);

        $pdoDsn = $mysqlConn->getMysqlPdoDsn("jdbc:mysql://localhost:3306/testdb?characterEncoding=UTF-8");
        $this->assertEquals("mysql:host=localhost;port=3306;dbname=testdb;charset=utf8", $pdoDsn);

        $pdoDsn = $mysqlConn->getMysqlPdoDsn("jdbc:mysql://localhost:3306/testdb?characterEncoding=UTF-8&after8012=false");
        $this->assertEquals("mysql:host=localhost;port=3306;dbname=testdb;charset=utf8", $pdoDsn);

        $pdoDsn = $mysqlConn->getMysqlPdoDsn("jdbc:mysql://localhost:3306/testdb?characterEncoding=UTF-8&after8012=true");
        $this->assertEquals("mysql:host=localhost;port=3306;dbname=testdb;charset=utf8mb4", $pdoDsn);
    }

    /**
     *
     * @test
     */
    public function unicode()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $charset = $mysqlConn->getMysqlCharset('UTF-8');
        $this->assertEquals('utf8', $charset);

        $charset = $mysqlConn->getMysqlCharset('UTF-8', true);
        $this->assertEquals('utf8mb4', $charset);

        $charset = $mysqlConn->getMysqlCharset('Cp1251');
        $this->assertEquals('cp1251', $charset);
    }

    /**
     *
     * @test
     */
    function pdoMysqlCharset()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $dsn = "jdbc:mysql://localhost:3306/testdb?characterEncoding=UTF-8";
        $pdoDsn = $mysqlConn->parsePdoMysqlCharset($dsn);
        $this->assertEquals(";charset=utf8", $pdoDsn);

        $dsn = "jdbc:mysql://localhost:3306/testdb?characterEncoding=UTF-8&after8012=false";
        $pdoDsn = $mysqlConn->parsePdoMysqlCharset($dsn);
        $this->assertEquals(";charset=utf8", $pdoDsn);

        $dsn = "jdbc:mysql://localhost:3306/testdb?characterEncoding=UTF-8&after8012=true";
        $pdoDsn = $mysqlConn->parsePdoMysqlCharset($dsn);
        $this->assertEquals(";charset=utf8mb4", $pdoDsn);
    }

    /**
     *
     * @test
     */
    public function pdoOptions()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $initCommand = 'SOMETHING';
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb?init=$initCommand";
        $options = $mysqlConn->parsePdoOptions($dsn);
        $this->assertEquals(array(
            PDO::MYSQL_ATTR_INIT_COMMAND => $initCommand
        ), $options);

        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $initCommand = 'SET NAMES utf8';
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb?init=$initCommand";
        $options = $mysqlConn->parsePdoOptions($dsn);
        $this->assertEquals(array(
            PDO::MYSQL_ATTR_INIT_COMMAND => $initCommand
        ), $options);

        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $initCommand = 'SET NAMES utf8';
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb";
        $options = $mysqlConn->parsePdoOptions($dsn);
        $this->assertEquals(null, $options);

        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $initCommand = 'SET NAMES utf8';
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb?init=&var1=var2";
        $options = $mysqlConn->parsePdoOptions($dsn);
        $this->assertEquals(null, $options);
    }

    /**
     *
     * @test
     */
    public function createInsertSelectDrop()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $initCommand = 'SET NAMES utf8';
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb?init=$initCommand";
        $mysqlConn->connect($dsn, 'root', '123456');
        $mysqlConn->runQuery("CREATE TABLE testdb.sometable (
	somefield varchar(100) NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci");
        $mysqlConn->runQuery("INSERT INTO testdb.sometable (somefield) values (?)", array(
            'áéíóúçãâêô'
        ));
        $stmt = $mysqlConn->runQuery("SELECT * FROM testdb.sometable");
        $result = $stmt->nextArray();
        $this->assertEquals(array(
            'somefield' => 'áéíóúçãâêô'
        ), $result);
        $stmt = $mysqlConn->runQuery("DROP TABLE testdb.sometable");
        $mysqlConn->disconnect();
    }

    /**
     *
     * @test
     */
    public function connectSelectInsertDbCharset()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb?characterEncoding=UTF-8&after8012=true";
        $mysqlConn->connect($dsn, 'root', '123456');

        $stmt = $mysqlConn->runQuery("select 'something'");
        $result = $stmt->nextArrayInt();
        $this->assertEquals('something', $result[0]);

        $stmt = $mysqlConn->runQuery("select 'áéíóúçãâêô'");
        $result = $stmt->nextArrayInt();
        $this->assertEquals('áéíóúçãâêô', $result[0]);

        $mysqlConn->runQuery("INSERT INTO testtable (utf8column) values (?)", array(
            'áéíóúçãâêô'
        ));

        $mysqlConn->disconnect();
    }

    /**
     *
     * @test
     */
    public function connectCount()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb?characterEncoding=UTF-8&after8012=true";
        $mysqlConn->connect($dsn, 'root', '123456');

        $stmt = $mysqlConn->runQuery("select 'something'");
        $total = $stmt->numRows();
        $this->assertEquals(1, $total);

        $mysqlConn->disconnect();
    }

    /**
     *
     * @test
     */
    public function connectSelectInsertDbPdoOptions()
    {
        $mysqlConn = new Rock_DbAl_PdoMySql_Conn();
        $initCommand = 'SET NAMES utf8';
        $dsn = "jdbc:mysql://127.0.0.1:3306/testdb?init=$initCommand";
        $mysqlConn->connect($dsn, 'root', '123456');
        $stmt = $mysqlConn->runQuery("select 'something'");
        $total = $stmt->numRows();
        $this->assertEquals(1, $total);
        $result = $stmt->nextArrayInt();
        $this->assertEquals('something', $result[0]);

        $stmt = $mysqlConn->runQuery("select 'áéíóúçãâêô'");
        $result = $stmt->nextArrayInt();
        $this->assertEquals('áéíóúçãâêô', $result[0]);

        $mysqlConn->runQuery("INSERT INTO testtable (utf8column) values (?)", array(
            'áéíóúçãâêô'
        ));

        $mysqlConn->disconnect();
    }
}

