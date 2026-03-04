<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NavigationHelper
{
    /**
     * Get navigation data for a specific model in a query context.
     * 
     * @param Model $model
     * @param Builder|null $query
     * @param string $orderBy
     * @param string $direction
     * @return array
     */
    public static function getNavigation(Model $model, ?Builder $query = null, ?string $orderBy = null, ?string $direction = null): array
    {
        if (!$query) {
            $query = $model->newQuery();
        }

        $keyName = $model->getTable() . '.' . $model->getKeyName();

        if ($orderBy) {
            $query->orderBy($orderBy, $direction ?: 'asc');
        } 
        
        // Add fallback sorting by ID to guarantee deterministic ordering
        // This prevents random jumping when sorting by non-unique columns (e.g. created_at)
        if ($orderBy !== $model->getKeyName() && $orderBy !== $model->getTable() . '.' . $model->getKeyName()) {
            $query->orderBy($model->getTable() . '.' . $model->getKeyName(), $direction ?: 'desc');
        }

        $allIds = (clone $query)->pluck($model->getKeyName())->toArray();
        $currentIndex = array_search($model->getKey(), $allIds);

        if ($currentIndex === false) {
            return [
                'prev' => null,
                'next' => null,
                'current' => 0,
                'total' => count($allIds)
            ];
        }

        // Generate URL query string to preserve context for next/prev
        $queryString = request()->except(['page', 'id']); // Keep filters but not current page/id

        return [
            'prev' => $currentIndex > 0 ? $allIds[$currentIndex - 1] : null,
            'next' => $currentIndex < count($allIds) - 1 ? $allIds[$currentIndex + 1] : null,
            'current' => $currentIndex + 1,
            'total' => count($allIds),
            'query' => $queryString
        ];
    }
}
