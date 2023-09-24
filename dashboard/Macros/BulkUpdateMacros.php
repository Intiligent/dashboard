<?php

namespace Dashboard\Macros;

use Illuminate\Database\Eloquent\Builder;

class BulkUpdateMacros {
    /**
     * The base query builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * Set of rows.
     *
     * @var array
     */
    protected $values;

    /**
     * Identify fields.
     *
     * @var string|array
     */
    protected $uniqueBy;

    /**
     * Create a new macros instance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $values
     * @param array $updates
     * @return void
     */
    public function __construct($builder, array $values, $uniqueBy, array $updates)
    {
        $this->builder = $builder;
        $this->values = $values;
        $this->updates = $updates;
        $this->uniqueBy = $uniqueBy;
    }

    /**
     * Compile raw sql query.
     *
     * @return string [sql query]
     */
    private function getSqlQuery(): string
    {
        $values = $this->values;
        $updates = $this->updates;
        $uniqueBy = $this->uniqueBy;
        if (!is_array(reset($values))) {
            $values = [$values];
        }
        if (!is_array($uniqueBy)) {
            $uniqueBy = [$uniqueBy];
        }
        $tableName = $this->builder->getModel()->getTable();

        // compile columns for raw sql
        $columns = collect($updates)->map(function($field) use ($values, $uniqueBy) {
            $cases = collect($values)->map(function($row) use ($field, $uniqueBy) {
                $conditions = collect($uniqueBy)->map(function($index) use ($row) {
                    return "`$index` = '$row[$index]'";
                })->implode(' AND ');
                $value = $row[$field];
                return "WHEN $conditions THEN '$value'";
            })->implode(' ');
            return "`$field` = (CASE $cases END)";
        })->implode(' ,');

        // compile where clause
        $where = collect($uniqueBy)->map(function($clause) use ($values) {
            $values = collect($values)->map(function($row) use ($clause) {
                return $row[$clause];
            })->implode("', '");
            return "`$clause` IN ('$values')";
        })->implode(' AND ');

        return "UPDATE `{$tableName}` SET {$columns} WHERE {$where}";
    }

    /**
     * Execute query for bulk update rows.
     *
     * @return int [total count of affected rows]
     */
    public function handle(): int
    {
        if (empty($this->values)) {
            return 0;
        }
        $connection = $this->builder->getQuery()->getConnection();
        return $connection->update($this->getSqlQuery());
    }
}
