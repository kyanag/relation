<?php

namespace Kyanag\Relation\Query;

use Kyanag\Relation\Interfaces\QueryInterface;
use Latitude\QueryBuilder\Query\SelectQuery;

use function Latitude\QueryBuilder\criteria;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\literal;

class Query implements QueryInterface
{

    /**
     * @var SelectQuery
     */
    protected $query;

    /**
     * @var Database
     */
    protected $db;

    public function __construct(SelectQuery $query, Database $db)
    {
        $this->query = $query;
        $this->db = $db;
    }

    /**
     * @param $table
     * @return $this
     */
    public function from($table)
    {
        $this->query->from($table);
        return $this;
    }

    public function where($column, $operator = null, $value = null)
    {
        if($column instanceof \Closure){
            call_user_func($column, $this);
            return $this;
        }
        if($value === null){
            //等于
            $value = $operator;
            $operator = "=";
        }
        $operator = strtolower($operator);
        switch ($operator){
            case "=":
            case "eq":
                //等于
                $this->query->andWhere(field($column)->eq($value));
                break;
            case "!=":
            case "<>":
            case "neq":
                //不等于
                $this->query->andWhere(field($column)->notEq($value));
                break;
            case ">":
            case "gt":
                $this->query->andWhere(field($column)->gt($value));
                break;
            case "<":
            case "lt":
                $this->query->andWhere(field($column)->lt($value));
                break;
            case ">=":
            case "gte":
                $this->query->andWhere(field($column)->gte($value));
                break;
            case "<=":
            case "lte":
                $this->query->andWhere(field($column)->lte($value));
                break;
            case "between":
                list($start, $end) = $value;
                $this->query->andWhere(field($column)->between($start, $end));
                break;
            case "in":
                $this->query->andWhere(field($column)->in($value));
                break;
            default:
                throw new \Exception("错误的sql操作符号：{$operator}");
        }
        return $this;
    }


    public function whereRaw($exp, $params = [])
    {
        $this->query->andWhere(criteria($exp, ...$params));
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this|Query
     */
    public function whereIn($column, $values = [])
    {
        $this->query->andWhere(field($column)->in(...$values));
        return $this;
    }

    public function whereNotIn($column, $values = [])
    {
        $this->query->andWhere(field($column)->notIn($values));
        return $this;
    }

    /**
     * @param $column
     * @param $start
     * @param $end
     * @return $this
     */
    public function whereNotBetween($column, $start, $end)
    {
        $this->query->andWhere(field($column)->notBetween($start, $end));
        return $this;
    }

    public function whereNull($column)
    {
        $this->query->andWhere(field($column)->isNull());
        return $this;
    }

    public function whereNotNull($column)
    {
        $this->query->andWhere(field($column)->isNotNull());
        return $this;
    }

    public function getAll()
    {
        $query = $this->query->compile();
        return $this->db->exec(
            $query->sql(),
            $query->params()
        );
    }


    public function getRawQuery()
    {
        return $this->query;
    }


    /**
     * @param $id
     * @return \Latitude\QueryBuilder\StatementInterface
     */
    public static function raw($id)
    {
        return literal($id);
    }
}