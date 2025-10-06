<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganizationScope
{
    /**
     * Boot the trait for a model.
     *
     * @return void
     */
    public static function bootBelongsToOrganizationScope(): void
    {
        static::creating(function ($model) {
            self::setOrganizationIdOnCreate($model);
        });

        static::addGlobalScope('organization', function (Builder $builder) {
            self::applyOrganizationScope($builder);
        });
    }

    /**
     * Set the organization ID on model creation if not already set.
     *
     * @param mixed $model
     * @return void
     */
    private static function setOrganizationIdOnCreate($model): void
    {
        if (!Auth::hasUser()) {
            return;
        }

        if (is_null($model->organization_id) && Auth::user()->organization_id) {
            $model->organization_id = Auth::user()->organization_id;
        }

        // Also set user_id if the model has this field and it's not set
        if (in_array('user_id', $model->getFillable()) && is_null($model->user_id)) {
            $model->user_id = Auth::id();
        }
    }

    /**
     * Apply the organization scope to the query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    private static function applyOrganizationScope(Builder $builder): void
    {
        if (!Auth::hasUser()) {
            return;
        }

        if (Auth::user()->organization_id) {
            $builder->where($builder->getModel()->getTable() . '.organization_id', Auth::user()->organization_id);
        } else {
            abort(404);
        }
    }
}
