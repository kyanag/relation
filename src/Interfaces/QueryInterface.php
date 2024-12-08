<?php

namespace Kyanag\Relation\Interfaces;

interface QueryInterface
{

    /**
     * @param $column
     * @param $values
     * @return static
     */
    public function whereIn($column, $values);


    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function where($column, $operator = null, $value = null);

    /**
     * @param $table
     * @return static
     */
    public function from($table);


    /**
     * @return mixed
     */
    public function getAll();
}