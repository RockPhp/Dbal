<?php

class Rock_DbAl_PdoMySql_Conn extends Rock_DbAl_Pdo_Conn implements Rock_DbAl_Iface_IConn
{

    public function getMysqlPdoDsn($dsn)
    {
        $arrayConn = array();
        $dsnArray = parse_url($dsn);
        $parseUrl = parse_url($dsnArray['path']);
        $arrayConn['host'] = $parseUrl['host'];
        $arrayConn['dbname'] = preg_replace("/[^A-z0-9_]/", '', $parseUrl['path']);
        $pdoDsn = 'mysql:host=' . $arrayConn['host'];
        $port = empty($parseUrl['port']) ? 3306 : $parseUrl['port'];
        $pdoDsn .= ';port=' . $port;
        $pdoDsn .= ';dbname=' . $arrayConn['dbname'];
        $pdoDsn .= $this->parsePdoMysqlCharset($dsn);
        return $pdoDsn;
    }

    public function parsePdoMysqlCharset($dsn)
    {
        $charsetPdoDsn = '';
        $dsnArray = parse_url($dsn);
        if (! empty($dsnArray) && ! empty($dsnArray['query'])) {
            $arrayQueryStr = array();
            parse_str($dsnArray['query'], $arrayQueryStr);
            if (! empty($arrayQueryStr['characterEncoding'])) {
                $after8012 = false;
                if (! empty($arrayQueryStr['after8012']) && $arrayQueryStr['after8012'] == 'true') {
                    $after8012 = true;
                }
                $charset = $this->getMysqlCharset($arrayQueryStr['characterEncoding'], $after8012);
                $charsetPdoDsn .= ';charset=' . $charset;
            }
        }
        return $charsetPdoDsn;
    }

    public function getMysqlCharset($unicode, $mysqlVersionAfter8012 = false)
    {
        $arrayMap = array(
            'ascii' => 'US-ASCII',
            'big5' => 'Big5',
            'gbk' => 'GBK',
            'sjis' => 'SJIS or Cp932',
            'cp932' => 'Cp932 or MS932',
            'gb2312' => 'EUC_CN',
            'ujis' => 'EUC_JP',
            'euckr' => 'EUC_KR',
            'latin1' => 'Cp1252',
            'latin2' => 'ISO8859_2',
            'greek' => 'ISO8859_7',
            'hebrew' => 'ISO8859_8',
            'cp866' => 'Cp866',
            'tis620' => 'TIS620',
            'cp1250' => 'Cp1250',
            'cp1251' => 'Cp1251',
            'cp1257' => 'Cp1257',
            'macroman' => 'MacRoman',
            'macce' => 'MacCentralEurope',
            'utf8' => 'UTF-8',
            'ucs2' => 'UnicodeBig'
        );
        $charset = array_search($unicode, $arrayMap);
        if ($charset == 'utf8' && $mysqlVersionAfter8012) {
            $charset = 'utf8mb4';
        }
        return $charset;
    }

    public function parsePdoOptions($dsn)
    {
        $options = null;
        $dsnArray = parse_url($dsn);
        if (! empty($dsnArray) && ! empty($dsnArray['query'])) {
            $arrayQueryStr = array();
            parse_str($dsnArray['query'], $arrayQueryStr);
            if (! empty($arrayQueryStr['init'])) {
                return array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => $arrayQueryStr['init']
                );
            }
        }
        return $options;
    }

    public function connect($dsn, $user = null, $passwd = null)
    {
        $pdoDsn = $this->getMysqlPdoDsn($dsn);
        parent::connectWithOptions($pdoDsn, $user, $passwd, $this->parsePdoOptions($dsn));
        $this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    public function disconnect()
    {
        parent::disconnect();
    }

    private function addLimit($sql, $start = null, $limit = null)
    {
        if (! empty($start) || ! empty($limit)) {
            $start = empty($start) ? 0 : $start;
            $sql .= ' LIMIT ' . $start . ',' . $limit;
        }
        return $sql;
    }

    public function runQuery($sql, array $arrayBind = array(), $start = null, $limit = null)
    {
        $sql = $this->addLimit($sql, $start, $limit);
        $stmt = parent::runQuery($sql, $arrayBind);
        return new Rock_DbAl_PdoMySql_Stmt($stmt);
    }

    public function getNewBind($value = null, $maxLenght = null, $type = null)
    {
        return parent::getNewBind($value, $maxLenght, $type);
    }

    public function setAutoCommit($autocommit = true)
    {
        parent::setAutoCommit($autocommit);
    }

    public function beginTransaction()
    {
        parent::beginTransaction();
    }

    public function commit()
    {
        parent::commit();
    }

    public function rollBack()
    {
        parent::rollBack();
    }

    public function getErrorMsg()
    {
        return parent::getErrorMsg();
    }

    public function getErrorCode()
    {
        return parent::getErrorCode();
    }

    public function insertId()
    {
        return parent::insertId();
    }
}
