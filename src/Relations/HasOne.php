<?php

namespace Kyanag\Relation\Relations;

class HasOne extends ForeignRelation
{

    protected $foreigner;

    protected $owner;

    protected $foreignKey;

    protected $ownerKey;

    protected $relation;

    /**
     * @param string $foreigner 附表表名
     * @param string $foreignKey 附表外键名
     * @param string $ownerKey 主表主键名
     * @param string|null $owner 主表
     * @param string|null $relation 关联名称
     */
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

        $foreignerRecords = array_column($foreignerRecords, null, $this->foreignKey);

        return array_map(function($record) use($foreignerRecords, $relationName){
            $record[$relationName] = $foreignerRecords[$record[$this->ownerKey]] ?? null;
            return $record;
        }, $records);
    }


}