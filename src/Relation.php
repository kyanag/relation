<?php

namespace Kyanag\Relation;

use Kyanag\Relation\Query\Database;
use Kyanag\Relation\Relations\BelongsTo;
use Kyanag\Relation\Relations\HasMany;
use Kyanag\Relation\Relations\HasOne;

class Relation
{
    /**
     * @param \PDO $pdo
     * @return RelationLoader
     */
    public static function createLoader(\PDO $pdo)
    {
        return new RelationLoader(
            new Database($pdo)
        );
    }


    /**
     * @param string $column   "主表.主键"
     * @param string $foreignColumn "附表.外键"
     * @return HasOne
     * @throws \Exception
     */
    public static function hasOne($column, $foreignColumn, $withs = [])
    {
        list($owner, $ownerKey) = static::resolveColumn($column);
        list($foreigner, $foreignKey) = static::resolveColumn($foreignColumn);
        if(is_null($foreigner)){
            throw new \Exception("缺少附表名称");
        }
        return (
            new HasOne($foreigner, $foreignKey, $ownerKey, $owner)
        )->with($withs);
    }

    /**
     * @param string $currentColumn "附表.外键"
     * @param string $mainColumn "主表.主键"
     * @return BelongsTo
     * @throws \Exception
     */
    public static function belongsTo($currentColumn, $mainColumn, $withs = [])
    {
        list($owner, $ownerKey) = static::resolveColumn($currentColumn);
        list($foreigner, $foreignKey) = static::resolveColumn($mainColumn);
        if(is_null($owner)){
            throw new \Exception("缺少主表名称");
        }
        return (
            new BelongsTo($foreigner, $foreignKey, $ownerKey, $owner)
        )->with($withs);
    }

    /**
     * @param string $column
     * @param string $foreignColumn
     * @return HasMany
     * @throws \Exception
     */
    public static function hasMany($column, $foreignColumn, $withs = [])
    {
        list($owner, $ownerKey) = static::resolveColumn($column);
        list($foreigner, $foreignKey) = static::resolveColumn($foreignColumn);
        if(is_null($foreigner)){
            throw new \Exception("缺少附表名称");
        }
        return (
            new HasMany($foreigner, $foreignKey, $ownerKey, $owner)
        )->with($withs);
    }

    /**
     * @param $column
     * @return array
     */
    protected static function resolveColumn($column)
    {
        $_ = array_filter(array_map("trim", explode(".", $column)));
        if(count($_) == 0){
            return [null, $column];
        }if(count($_) == 1){
            return [null, $_[0]];
        }else{
            return [$_[0], $_[1]];
        }
    }
}