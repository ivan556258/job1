<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;

class DoubleRegistrationDetectedController extends Controller
{
    public function index(User $user)
    {
        $data = [
            'title' => 'Обнаружена мультирегистрация',
            'user' => $user
        ];

        return view('auth.double-registration', $data);
    }
}
