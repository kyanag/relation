<?php

namespace Kyanag\Relation\Query;


use Kyanag\Relation\Interfaces\DatabaseInterface;
use Latitude\QueryBuilder\Engine\CommonEngine;
use Latitude\QueryBuilder\Engine\MySqlEngine;
use Latitude\QueryBuilder\Engine\PostgresEngine;
use Latitude\QueryBuilder\Engine\SqliteEngine;
use Latitude\QueryBuilder\Engine\SqlServerEngine;
use Latitude\QueryBuilder\QueryFactory;

class Database implements DatabaseInterface
{

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var \PDO
     */
    protected $pdo;

    protected $sqls = [];

    public function __construct(\PDO $pdo, $type = null)
    {
        $this->pdo = $pdo;

        $this->initQueryFactory(
            $type ?: ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME))
        );
    }


    protected function initQueryFactory(string $type)
    {
        switch (strtolower($type)){
            case "mysql":
                $engine = new MySqlEngine();
                break;
            case "pgsql":
                $engine = new PostgresEngine();
                break;
            case "sqlsrv":
                $engine = new SqlServerEngine();
                break;
            case "sqlite":
                $engine = new SqliteEngine();
                break;
            default:
                $engine = new CommonEngine();
                break;
        }
        $this->queryFactory = new QueryFactory($engine);
    }


    /**
     * @param $table
     * @return Query
     */
    public function newQuery($table = null)
    {
        $query = $this->queryFactory->select();
        if($table !== null){
            $query = $query->from($table);
        }
        return new Query($query, $this);
    }

    public function getSqls()
    {
        return $this->sqls;
    }

    public function exec(string $sql, $params = [])
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $res = $statement->fetchAll();

        $this->logSql($sql, $params);
        return $res;
    }

    protected function logSql($sql, $params)
    {
        $params = array_map(function ($value){
            return is_numeric($value) ? $value : "'{$value}'";
        }, $params);
        $this->sqls[] = vsprintf(str_replace('?', '%s', $sql), $params);
    }
}