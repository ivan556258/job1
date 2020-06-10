<?php

namespace App\Http\Controllers\Admin;

use App\Facades\SalonAdvertising;
use App\User;
use App\AdvertisingBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AdvertisingBannersController extends \App\Http\Controllers\Account\AdvertisingBannersController
{
    protected $storeRouteName = 'admin.advertising.index';
    protected $updateRouteName = 'admin.advertising.index';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Баннеры',
            'usersAdvertising' => AdvertisingBanner::with(['user', 'city', 'country'])->paginate(25)
        ];

        return view('admin.advertising.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Добавить баннер',
            'profiles' => []
        ];

        return view('admin.advertising.create', $data);
    }

    public function edit(AdvertisingBanner $advertising)
    {
        $advertising->load(['user']);
        $data = [
            'title' => 'Редактирование рккламного баннера',
            'advertising' => $advertising,
            'profiles' => $advertising->user->hasRole('individual') ? $advertising->user->profiles : []
        ];

        return view('admin.advertising.edit', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AdvertisingBanner $advertising
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(AdvertisingBanner $advertising)
    {
        $json['success'] = false;
        try {
            $advertising->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can\'t remove advertising banner', ['message' => $e->getMessage(), 'advertisingBanner' => $advertising]);
        }

        return Response::json($json);
    }

    public function toggleActivate(AdvertisingBanner $userAdvertising)
    {
        $json['success'] = false;
        $userAdvertising->user->advertising()->update([
            'active' => !$userAdvertising->active
        ]);

        $json['status'] = $userAdvertising->refresh()->active;
        $json['success'] = true;

        return Response::json($json);
    }

    protected function getUser(Request $request)
    {
        return User::whereKey($request->input('user_id'))->first() ?? Auth::user();
    }
}
