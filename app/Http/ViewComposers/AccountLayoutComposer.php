<?php
namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountLayoutComposer
{
    public function compose(View $view)
    {
        if (Auth::check() && Auth::user()->getRoles()->isEmpty()) {
            $view->with('showSelectUserRole', true);
        }
    }
}