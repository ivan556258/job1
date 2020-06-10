<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Profile;
use App\Salon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class FavoriteController extends Controller
{
    public function index()
    {
        $profileRelations = $this->getRelations(config('relations.girl-card'));
        $salonRelations = $this->getRelations(config('relations.salon-card'));
        $profiles = Auth::user()->favorite(Profile::class)->with($profileRelations)
            ->paginate(4, ['*'], 'profilesPage')->appends(Input::except('profilesPage'));
        $salons = Auth::user()->favorite(Salon::class)->with($salonRelations)
            ->paginate(4, ['*'], 'salonsPage')->appends(Input::except('salonsPage'));

        $data = [
            'meta_title' => 'Избранное',
            'profiles' => $profiles,
            'salons' => $salons
        ];

        return view('front.account.favorites.index', $data);
    }

    private function getRelations(array $relations)
    {
        $arr = [];

        foreach ($relations as $relation) {
            $arr[] = 'favoriteable.' . $relation;
        }

        return $arr;
    }
}
