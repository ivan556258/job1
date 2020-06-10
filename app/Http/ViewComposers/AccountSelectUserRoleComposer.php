<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use jeremykenedy\LaravelRoles\Models\Role;

class AccountSelectUserRoleComposer
{
    public function compose(View $view)
    {
        $view->with('roles', Role::where('level', '<', config('general.roles.allowed_role_registration_level'))->get());
    }
}