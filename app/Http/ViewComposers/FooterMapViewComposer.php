<?php


namespace App\Http\ViewComposers;


use App\Facades\Domain;
use App\Profile;
use App\Salon;
use Illuminate\View\View;

class FooterMapViewComposer
{
    public function compose(View $view)
    {
        $data = $view->getData();

        if(empty($data['salons_map'])) {
            $view->with('salons_map', Salon::all(['id', 'map_coordinates', 'name']));
        }

        if(empty($data['individual_map'])) {
            $view->with('individuals_map', Profile::all(['id', 'map_coordinates', 'name']));
        }

        if(empty($data['city'])) {
            $view->with('city', Domain::getDomainCity());
        }
    }
}