<?php

namespace App\Http\Controllers\Account;

use App\Facades\SalonAdvertising;
use App\Facades\SalonService;
use App\Facades\SalonTransactions;
use App\Http\Controllers\Controller;
use App\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SalonController extends Controller
{
    public function index()
    {
        $data = [
            'meta_title' => 'Настройки салона',
            'user' => Auth::user(),
            'salon' => Auth::user()->salon
        ];

        return view('front.account.salon.index', $data);
    }

    public function update(Request $request, Salon $salon)
    {
        $this->validateForm($request);

        $salon = Auth::user()->salon()->updateOrCreate([], $request->except(['active']));
        SalonService::createExtraData($request, $salon);

        return redirect()->route('account.salon.index')->with(['status' => 'success', 'message' => 'Настройки успешно сохранены']);
    }

    public function favorite(Request $request, Salon $salon)
    {
        $json['success'] = false;
        $user = Auth::user();

        if($request->input('list')) {
            $user->setList($request->input('list'));
        }

        $user->toggleFavorite($salon);
        $json['success'] = true;
        $json['isInList'] = $user->isFavorited($salon);

        return Response::json($json);
    }

    public function activate()
    {
        $json['success'] = false;
        if(SalonTransactions::setActive()) {
            $json['success'] = true;
        }

        return Response::json($json);
    }

    public function riseSalon()
    {
        $json['success'] = false;

        if (SalonAdvertising::rise()) {
            $json['success'] = true;
        }

        return Response::json($json);
    }

    protected function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $v->validate();
    }
}
