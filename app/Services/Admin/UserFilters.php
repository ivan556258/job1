<?php

namespace App\Services\Admin;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilters
{
    public static function createQuery(Request $request, Builder $users): Builder
    {
        if ($request->input('name')) {
            $users->where('name', 'LIKE', '%' . $request->input('name') . '%');
        }

        if ($request->input('username')) {
            $users->where('username', 'LIKE', '%' . $request->input('username') . '%');
        }

        if ($request->input('email')) {
            $users->where('email', 'LIKE', '%' . $request->input('email') . '%');
        }

        if ($request->input('telephone')) {
            $users->where('telephone', 'LIKE', '%' . $request->input('telephone') . '%');
        }

        if ($request->input('city_id')) {
            $users->where('city_id', $request->input('city_id'));
        }

        return $users;
    }

    public static function getVariables(Request $request): array
    {
        $arr = [];
        foreach ($request->all() as $filter => $value) {
            $arr['filter' . str_replace("_", "", ucwords($filter, "_"))] = $value;
        }

        return $arr;
    }
}
