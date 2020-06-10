<?php

namespace App\Http\Controllers;

use App\Facades\Domain;
use App\Salon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalonController extends Controller
{
    public function index()
    {
        $city = Domain::getDomainCity();

        $data = [
            'salons' => Salon::with(config('relations.salon-card'))->paginate(20),
            'city' => $city,
            'newest' => Salon::newest()->get(),
            'salons_map' => Salon::all(['id', 'name', 'map_coordinates'])
        ];

        return view('front.salons.index', $data);
    }

    public function show(Salon $salon)
    {
        $salon->load(config('relations.salons.show'));
        $data = [
            'meta_title' => 'Салон ' . $salon->name . ', г.' . $salon->user->city->name . ', #' . $salon->id,
            'meta_description' => Str::limit($salon->description),
            'salon' => $salon,
            'profiles' => $salon->user->profiles()->with(config('relations.girl-card'))->paginate(8),
            'otherSalons' => Salon::whereKeyNot($salon->id)->orderBy(DB::raw('RAND()'))->take(12)->get(),
            'city' => Domain::getDomainCity()
        ];

        return view('front.salons.show', $data);
    }
}
