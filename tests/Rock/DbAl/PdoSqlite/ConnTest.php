<?php

final class ConnTest extends PHPUnit_Framework_TestCase
{

    private function getDbPath()
    {
        return dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/mydb.sq3';
    }

    /**
     *
     * @test
     */
    public function testDbPath()
    {
        $dbPath = $this->getDbPath();
        $this->assertEquals('/home/nils/git/php/Rock/Dbal/mydb.sq3', $dbPath);
    }

    /**
     *
     * @test
     */
    public function connectCreateInsertSelectDrop()
    {
        $conn = new Rock_DbAl_PdoSqlite_Conn();
        $conn->connect("sqlite::memory:");
        $this->createTable($conn);
        $this->insertSelectAssert($conn);
        $this->insertSelectWhereBindAssert($conn);
        $this->selectCountAssert($conn);
        $this->selectWithLimitCountAssert($conn);
        $this->selectWithBindLimitCountAssert($conn);
        $this->dropTable($conn);
        $conn->disconnect();
    }

    /**
     *
     * @param
     *            Rock_DbAl_PdoSqlite_Conn
     */
    private function selectWithBindLimitCountAssert($conn)
    {
        $stmt = $conn->runQuery("SELECT * FROM NEWTABLE where SOMECOLUMN = :somebind", array(
            'somebind' => 'foi'
        ), 0, 2);
        $sqlCount = $stmt->getQueryCount();
        $this->assertEquals("SELECT COUNT(1) FROM (SELECT * FROM NEWTABLE where SOMECOLUMN = :somebind LIMIT 0,2)", $sqlCount);
        $total = $stmt->numRows();
        $this->assertEquals(1, $total);
        $stmt->closeCursor();
    }

    /**
     *
     * @param
     *            Rock_DbAl_PdoSqlite_Conn
     */
    private function selectWithLimitCountAssert($conn)
    {
        $stmt = $conn->runQuery("SELECT * FROM NEWTABLE", array(), 0, 10);
        $sqlCount = $stmt->getQueryCount();
        $this->assertEquals("SELECT COUNT(1) FROM (SELECT * FROM NEWTABLE LIMIT 0,10)", $sqlCount);
        $total = $stmt->numRows();
        $this->assertEquals(10, $total);
        $stmt->closeCursor();
    }

    /**
     *
     * @param
     *            Rock_DbAl_PdoSqlite_Conn
     */
    private function selectCountAssert($conn)
    {
        $stmt = $conn->runQuery("SELECT * FROM NEWTABLE");
        $sqlCount = $stmt->getQueryCount();
        $this->assertEquals("SELECT COUNT(1) FROM (SELECT * FROM NEWTABLE)", $sqlCount);
        $total = $stmt->numRows();
        $this->assertEquals(21, $total);
        $stmt->closeCursor();
    }

    /**
     *
     * @param
     *            Rock_DbAl_PdoSqlite_Conn
     */
    private function insertSelectWhereBindAssert($conn)
    {
        $conn->runQuery("INSERT INTO NEWTABLE (SOMECOLUMN) VALUES (:something)", array(
            'something' => 'foi'
        ));
        $stmt = $conn->runQuery("SELECT * FROM NEWTABLE where SOMECOLUMN = :somebind", array(
            'somebind' => 'foi'
        ));
        $retorno = $stmt->nextArray();
        $this->assertEquals(array(
            'SOMECOLUMN' => 'foi'
        ), $retorno);
        $stmt->closeCursor();
    }

    /**
     *
     * @param
     *            Rock_DbAl_PdoSqlite_Conn
     */
    private function insertSelectAssert($conn)
    {
        for ($i = 0; $i < 20; $i ++) {
            $conn->runQuery("INSERT INTO NEWTABLE (SOMECOLUMN) VALUES (:something)", array(
                'something' => 'vai'
            ));
        }
        $stmt = $conn->runQuery("SELECT * FROM NEWTABLE");
        $retorno = $stmt->nextArray();
        $this->assertEquals(array(
            'SOMECOLUMN' => 'vai'
        ), $retorno);
        $stmt->closeCursor();
    }

    /**
     *
     * @param
     *            Rock_DbAl_PdoSqlite_Conn
     */
    private function dropTable($conn)
    {
        $conn->runQuery("DROP TABLE NEWTABLE");
    }

    /**
     *
     * @param
     *            Rock_DbAl_PdoSqlite_Conn
     */
    private function createTable($conn)
    {
        $conn->runQuery("CREATE TABLE NEWTABLE (
        SOMECOLUMN VARCHAR(100))");
    }
}

