<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Carbon;

class ProfilesActiveScope implements Scope {

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('active', 1)->where('paid', 1)
            ->where(function ($query) {
            $query->where('verification', 1)
            ->orWhere(function ($orWhere) {
                $orWhere->where('verification_paid', 1)
                    ->where('verification_at', '>=', Carbon::today()->addDays(-30));
            });
        });
    }
}