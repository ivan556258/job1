<?php


namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Filters
{
    protected $guardedFilters = [];

    //Применяет фильтры к передоваемой модели
    public function applyFilters(Request $request, $model)
    {
        foreach ($request->all() as $key => $value) {
            if (!in_array($key, $this->guardedFilters) || $this->guard()) {
                if ($value !== null && method_exists($this, $key)) {
                    $this->$key($value, $model);
                }
            }
        }

        return $model;
    }

    //Вернет переменные со значениями фильтров
    public function getFiltersVariables(Request $request): array
    {
        $arr = [];
        foreach ($request->all() as $filter => $value) {
            $arr['filter' . str_replace("_", "", ucwords($filter, "_"))] = $value;
        }

        return $arr;
    }

    //Проверка для тех фильтров, которые должны быть доступны только админским пользователям
    private function guard()
    {
        return Auth::check() && Auth::user()->level() > 4;
    }
}