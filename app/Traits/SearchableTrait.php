<?php


namespace App\Traits;


trait SearchableTrait
{
    /**
     * @param $query
     * @param string $search
     * @param array $columns
     * @param array $whereHas
     *
     * $whereHas = [$relation, $column, $value]
     */
    public function scopeSearch($query, string $search, array $columns = ['username'], array $whereHas = [])
    {
        $query->where(function ($whereQuery) use ($columns, $search) {
            foreach ($columns as $key => $column) {
                if (isset($column) && $this->isFillable($column)) {
                    $whereQuery->orWhere(function ($orWhereQuery) use ($column, $search) {
                        $orWhereQuery->where($column, 'LIKE', '%' . $search . '%');
                    });
                }
            }
        });

        if ($whereHas) {
            foreach ($whereHas as $column) {
                if (method_exists($this, $column[0]) && !empty($column[2])) {
                    $query->whereHas($column[0], function ($relation) use ($column) {
                        $relation->where($column[0] . '.' . $column[1], $column[2]);
                    });
                }
            }
        }
    }
}