<?php

/**
 *  test case.
 */
class ConnTest extends PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        // TODO Auto-generated Test::setUp()
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated Test::tearDown()
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     *
     * @test
     */
    public function checkPgConnectDsn()
    {
        $conn = new Rock_DbAl_Pgsql_Conn();
        $pgConnectDsn = $conn->getPgConnectDsn("jdbc:postgresql://host:1234/database", "postgres", "postgres");
        $this->assertEquals("host=host port=1234 dbname=database user=postgres password=postgres", $pgConnectDsn);

        $pgConnectDsn = $conn->getPgConnectDsn("jdbc:postgresql://host/database", "postgres", "postgres");
        $this->assertEquals("host=host port=5432 dbname=database user=postgres password=postgres", $pgConnectDsn);

        $pgConnectDsn = $conn->getPgConnectDsn("jdbc:postgresql://host/database", "postgres");
        $this->assertEquals("host=host port=5432 dbname=database user=postgres", $pgConnectDsn);

        $pgConnectDsn = $conn->getPgConnectDsn("jdbc:postgresql://host/database");
        $this->assertEquals("host=host port=5432 dbname=database", $pgConnectDsn);
    }

    /**
     *
     * @test
     */
    public function checkPgConenction()
    {
        $conn = new Rock_DbAl_Pgsql_Conn();
        $conn->connect("jdbc:postgresql://127.0.0.1/postgres", "postgres", "postgres");
        $conn->disconnect();
    }
}

