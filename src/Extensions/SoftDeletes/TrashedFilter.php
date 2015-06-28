<?php

namespace Brouwers\LaravelDoctrine\Extensions\SoftDeletes;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TrashedFilter extends SQLFilter
{
    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetadata $metadata
     * @param string        $table
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $metadata, $table)
    {
        return $this->isSoftDeletable($metadata->rootEntityName) ? "{$table}.deleted_at IS NULL OR CURRENT_TIMESTAMP < {$table}.deleted_at" : '';
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    protected function isSoftDeletable($entity)
    {
        return array_key_exists(SoftDeletes::class, class_uses_recursive($entity));
    }
}
