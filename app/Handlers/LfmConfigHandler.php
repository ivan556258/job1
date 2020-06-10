<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Auth;

class LfmConfigHandler extends \UniSharp\LaravelFilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        if(\Auth::user()->hasRole('admin')) {
            return 'users';
        }

        return 'users/' . Auth::user()->id;
    }
}
