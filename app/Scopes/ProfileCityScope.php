<?php
namespace App\Scopes;

use App\Facades\Domain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class ProfileCityScope implements Scope
{
    private $city_id;

    public function __construct(int $city_id = null)
    {
        if($city_id) {
            $this->city_id = $city_id;
        } else {
            $this->city_id = Domain::getDomainCity()->id;
        }
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereHas('user', function ($query) {
            $query->where('city_id', $this->city_id);
        });
    }
}