<?php

namespace AbdulRehman56\DynamicFilter;

class DynamicFilter
{
    public static function applyNestedWhereHas($request, $query)
    {
        $filters = $request->filters ? $request->filters : [];

        foreach ($filters as $filter) {
            if (isset($filter['have'])) {
                // Handle relation-based filtering
                $relation = $filter['have'];
                $query->whereHas($relation, function ($query) use ($filter) {
                    self::applySubFilters($query, $filter);
                });
            } elseif (isset($filter['does_not_have'])) {
                // Handle absence of relation
                $relation = $filter['does_not_have'];
                $query->doesntHave($relation);
            } else {
                // Existing filter handling
                self::applySubFilters($query, $filter);
            }
        }

        if ($request->has('sort_by') && $request->has('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        }
    }

    private static function applySubFilters($query, $filter)
    {
        if (isset($filter['or'])) {
            $query->where(function ($query) use ($filter) {
                foreach ($filter['or'] as $orFilter) {
                    self::applyFilter($query, $orFilter, 'orWhere');
                }
            });
        } elseif (isset($filter['and'])) {
            $query->where(function ($query) use ($filter) {
                foreach ($filter['and'] as $andFilter) {
                    self::applyFilter($query, $andFilter);
                }
            });
        } else {
            self::applyFilter($query, $filter);
        }
    }

    private static function applyFilter($query, $filter, $method = 'where')
    {
        if (isset($filter['column_name'], $filter['operator'], $filter['value'])) {
            $column_name = $filter['column_name'];
            $operator = $filter['operator'];
            $value = $filter['value'];

            $relations = explode('.', $column_name);
            $column = array_pop($relations);

            // Check if the operator is 'in' to apply a 'whereIn' condition
            $isInCondition = strtolower($operator) === "in";

            // Recursive closure to apply 'whereHas' for each level of relationship nesting
            $applyNestedWhereHas = function ($query, $relations, $method) use (&$applyNestedWhereHas, $column, $operator, $value, $filter, $isInCondition) {
                // Base case: no more relations, apply the actual filter
                if (empty($relations)) {
                    if (isset($filter['and']) || isset($filter['or'])) {
                        // If there are nested logical operations within the relationship
                        self::applySubFilters($query, $filter);
                    } else {
                        // Apply 'whereIn' or 'where' based on the operator
                        if ($isInCondition) {
                            $query->whereIn($column, $value);
                        } else {
                            $query->{$method}($column, $operator, $value);
                        }
                        return $query;
                    }
                }

                // Recursive case: apply 'whereHas' for the next level of relationship nesting
                $relation = array_shift($relations);
                return $query->{$method . 'Has'}($relation, function ($query) use ($relations, $applyNestedWhereHas) {
                    $applyNestedWhereHas($query, $relations, 'where');
                });
            };

            // Start the recursion with the initial query and relations
            $applyNestedWhereHas($query, $relations, $method);
        }
    }
}
