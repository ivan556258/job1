<?php

namespace App\Http\Controllers\Account;

use App\AdvertisingBanner;
use App\Facades\SalonAdvertising;
use App\Http\Controllers\Controller;
use App\Salon;
use App\Scopes\SalonActiveScope;
use App\Scopes\SalonCityScope;
use App\Scopes\SalonHasImageScope;
use App\Scopes\SalonOrderScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AdvertisingBannersController extends Controller
{
    protected $storeRouteName = 'account.advertising.index';
    protected $updateRouteName = 'account.advertising.index';

    public function index()
    {
        DB::statement(DB::raw('set @row:=0'));
        Salon::addGlobalScope(new SalonActiveScope());
        Salon::addGlobalScope(new SalonHasImageScope());
        Salon::addGlobalScope(new SalonCityScope(Auth::user()->city_id));
        Salon::addGlobalScope(new SalonOrderScope());
        $salon = Salon::select(['*', DB::raw('@row:=@row+1 as row')])->get();
        $lastUnavailableDay = SalonAdvertising::getLastUnavailableDay(Auth::user()->city);

        $data = [
            'meta_title' => 'Рекламный баннер',
            'advertisings' => Auth::user()->advertising,
            'salon' => Auth::user()->getRoles()->isNotEmpty() && Auth::user()->getRole()->slug == 'salon' && Auth::user()->salon ? $salon->where('id', Auth::user()->salon->id)->first() : false,
            'profiles' => Auth::user()->profiles,
            'bannersNotAvailable' => $lastUnavailableDay !== Carbon::today()->format('d.m.Y') ? $lastUnavailableDay : false
        ];

        return view('front.account.advertising.index', $data);
    }

    public function store(Request $request)
    {
        $this->validateForm($request);
        SalonAdvertising::buyBanner($request, $this->getUser($request));

        return redirect()->route($this->storeRouteName);
    }

    public function update(Request $request, AdvertisingBanner $advertising)
    {
        $this->validateForm($request, $advertising);
        SalonAdvertising::buyBanner($request, $this->getUser($request));

        return redirect()->route($this->updateRouteName);
    }

    public function disableAdvertising()
    {
        $json['success'] = false;
        Auth::user()->advertising()->delete();
        $json['success'] = true;

        return Response::json($json);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnAvailableDays(Request $request)
    {
        $json['success'] = false;
        $user = $this->getUser($request);

        if($user->city_id) {
            if($request->input('type') === 'city') {
                $localisation = $user->city;
            } else {
                $localisation = $user->city->region->country;
            }

            $json['unAvailableDays'] = SalonAdvertising::getUnAvailableDays($localisation);
            $json['success'] = true;
        }

        return Response::json($json);
    }

    protected function getUser(Request $request)
    {
        return Auth::user();
    }

    protected function validateForm(Request $request, AdvertisingBanner $advertisingBanner = null)
    {
        $v = \Validator::make($request->all(), [
            'adv.*.title' => 'required|max:255',
            'adv.*.description' => 'required|max:255',
            'adv.*.date_start' => 'required|date',
            'adv.*.days' => 'required|numeric',
            'adv.*.type' => 'required|string',
            'adv.*.link' => 'nullable|url',
            'adv.*.profile_id' => 'sometimes|required|integer',
            'user_id' => 'sometimes|required|integer'
        ]);

        $user = $this->getUser($request);

        $advertisingBannerId = null;
        if($advertisingBanner) {
            $advertisingBannerId = $advertisingBanner->id;
        }

        foreach ($request->input('adv') as $key => $item) {
            $v->sometimes('adv.'.$key.'.date_start', 'after:today', function ($input) {
                return Auth::user()->level() < 3;
            });

            $v->sometimes('adv.'.$key.'.date_start', 'availableAdvertisingBannersRange:'.$user->id.',adv.'.$key.'.type,adv.'.$key.'.days,'.$advertisingBannerId, function ($input) use ($advertisingBanner, $key) {
                $input = $input->get('adv');
                if(empty($input[$key]['date_start'])) {
                    return false;
                }
                $date_start = Carbon::createFromFormat('d.m.Y', $input[$key]['date_start'])->setTime(0, 0);
                return empty($advertisingBanner) || $advertisingBanner->date_start != $date_start || $advertisingBanner->date_finish != $date_start->copy()->addDays($input[$key]['days']);
            });

            $v->sometimes('adv.'.$key.'.image_banner', 'required|mimetypes:image/jpeg,image/png,image/gif|dimensions:width=370,height=200', function ($input) use ($advertisingBanner, $key) {
                $input = $input->get('adv');
                return empty($advertisingBanner) || !empty($input[$key]['image_banner']);
            });
        }

        $v->validate();
    }
}
