<?php

namespace Kyanag\Relation\Relations;

interface RelationInterface
{

    public function match($records, $foreignerRecords, $relationName = null);

    public function with($relations = []);

    public function getLoadingRelations();
}