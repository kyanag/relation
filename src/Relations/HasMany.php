<?php

namespace Kyanag\Relation\Relations;

use Kyanag\Relation\Helper;

class HasMany extends ForeignRelation
{

    protected $foreigner;

    protected $owner;

    protected $foreignKey;

    protected $ownerKey;

    protected $relation;

    public function __construct($foreigner, $foreignKey, $ownerKey, $owner = null, $relation = null)
    {
        if($relation === null){
            $relation = $foreigner;
        }

        $this->foreigner = $foreigner;
        $this->owner = $owner;
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
        $this->relation = $relation;
    }

    public function match($records, $foreignerRecords, $relationName = null)
    {
        $relationName = $relationName ?: $this->relation;

        $foreignerRecords = Helper::arrayGroup($foreignerRecords, $this->foreignKey);

        return array_map(function($record) use($foreignerRecords, $relationName){
            $record[$relationName] = $foreignerRecords[$record[$this->ownerKey]] ?? [];
            return $record;
        }, $records);
    }
}