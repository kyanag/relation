<?php

namespace Kyanag\Relation\Relations;

use Kyanag\Relation\Query\Query;
use Kyanag\Relation\RelationLoader;

/**
 *
 * @mixin Query
 */
abstract class ForeignRelation implements \ArrayAccess, RelationInterface
{

    protected $queryCallables = [];


    protected $loadRelations = [];


    public function offsetGet($offset)
    {
        if(isset($this->{$offset})){
            return $this->{$offset};
        }
        return null;
    }


    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException();
    }


    public function offsetExists($offset)
    {
        throw new \BadMethodCallException();
    }


    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException();
    }


    public function __call($name, $arguments)
    {
        $this->queryCallables[] = function($query) use($name, $arguments){
            return $query->{$name}(...$arguments);
        };
        return $this;
    }


    public function getQueryCallables()
    {
        return $this->queryCallables;
    }


    public function with($relations = [])
    {
        $this->loadRelations = array_replace($this->loadRelations, $relations);
        return $this;
    }

    public function getLoadingRelations()
    {
        return $this->loadRelations;
    }

    /**
     * @param RelationLoader $loader
     * @param array $records
     * @return array
     */
    public function getAll($loader, $records)
    {
        $relation_ids = array_filter(array_column($records, $this->ownerKey), function($item){
            return $item !== null;
        });
        $query = $loader->newQuery($this->foreigner)
            ->whereIn($this->foreignKey, $relation_ids);

        foreach ($this->queryCallables as $queryCallable)
        {
            call_user_func_array($queryCallable, [$query]);
        }
        return $query->getAll();
    }
}