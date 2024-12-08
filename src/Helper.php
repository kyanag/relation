<?php

namespace Kyanag\Relation;

use Buki\Pdox;
use Kyanag\Relation\Providers\BaseProvider;
use Kyanag\Relation\Supports\PDOPdoxAdapter;

class Helper
{

    /**
     * @param array $items
     * @param callable|string $groupBy
     * @return array
     */
    public static function arrayGroup(array $items, $groupBy)
    {
        $keyGetter = $groupBy;
        if(!is_callable($groupBy))
        {
            $keyGetter = function($item, $index) use($groupBy){
                return $item[$groupBy] ?? null;
            };
        }
        $res = [];
        foreach ($items as $index => $item)
        {
            $key = $keyGetter($item, $index);
            if(!isset($res[$key])){
                $res[$key] = [
                    $item
                ];
            }else{
                $res[$key][] = $item;
            }
        }
        return $res;
    }

}