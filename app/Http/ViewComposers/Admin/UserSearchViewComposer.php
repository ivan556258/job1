<?php


namespace App\Http\ViewComposers\Admin;


use Illuminate\Support\Facades\Request;
use Illuminate\View\View;

class UserSearchViewComposer
{
    public function compose(View $view)
    {
        $view->with('userSearchRole', Request::input('role'));
    }
}