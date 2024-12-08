<?php

namespace Kyanag\Relation\Interfaces;

interface DatabaseInterface
{

    /**
     * @param $table
     * @return QueryInterface
     */
    public function newQuery($table);


    /**
     * @return mixed
     */
    public function getSqls();
}