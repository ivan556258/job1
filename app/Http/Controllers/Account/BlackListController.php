<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Profile;
use Illuminate\Support\Facades\Auth;

class BlackListController extends Controller
{
    public function index()
    {
        $relations = [];
        $defaultRelations = config('relations.girl-card');

        foreach ($defaultRelations as $relation) {
            $relations[] = 'favoriteable.' . $relation;
        }

        $data = [
            'meta_title' => 'Черный список',
            'profiles' => Auth::user()->setList('BlackList')->favorite(Profile::class)
                ->with($relations)->paginate(25)
        ];

        return view('front.account.blacklist.index', $data);
    }
}
