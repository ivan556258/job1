<?php


namespace App\Services;


class CityFilter extends Filters
{
    protected $guardedFilters = [
        'city_name'
    ];

    protected function city_name($value, $cities)
    {
        return $cities->where('cities.name', 'LIKE', '%' . $value . '%');
    }
}