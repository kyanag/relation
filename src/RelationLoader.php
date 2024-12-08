<?php

namespace Kyanag\Relation;

use Kyanag\Relation\Interfaces\DatabaseInterface;
use Kyanag\Relation\Interfaces\QueryInterface;
use Kyanag\Relation\Relations\RelationInterface;

class RelationLoader
{

    protected $database;


    public function __construct(DatabaseInterface $queryable)
    {
        $this->database = $queryable;
    }


    /**
     * @param array $records
     * @param array $relations
     * @return array
     */
    public function load($records, $relations = [])
    {
        /** @var RelationInterface $relation */
        foreach ($relations as $key => $relation)
        {
            $relationRecords = $this->fetchRelationData($records, $relation);
            $records = $relation->match($records, $relationRecords, $key);
        }
        return $records;
    }

    /**
     * @param mixed $record
     * @param array $relations
     * @return mixed
     * @throws \Exception
     */
    public function loadOne($record, $relations = [])
    {
        $records = $this->load([$record], $relations);
        if(is_array($records) && count($records) == 1){
            return $records[0];
        }
        throw new \Exception("loader error");
    }

    /**
     * @param $items
     * @param RelationInterface $relation
     * @return array|mixed
     */
    public function fetchRelationData($items, $relation)
    {
        $foreignRecords = $this->fetchForeignData($items, $relation);

        $relations = $relation->getLoadingRelations();
        if(count($relations) > 0){
            $foreignRecords = $this->load($foreignRecords, $relations);
        }
        return $foreignRecords;
    }

    /**
     * @param array $records
     * @param RelationInterface $relation
     * @return mixed
     */
    public function fetchForeignData($records, $relation)
    {
        return $relation->getAll($this, $records);
    }


    /**
     * @param string $table
     * @return QueryInterface
     */
    public function newQuery($table = null): QueryInterface
    {
        return $this->database->newQuery($table);
    }


    /**
     * @return DatabaseInterface
     */
    public function getDatabase(): DatabaseInterface
    {
        return $this->database;
    }
}