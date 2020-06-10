<?php

namespace App\Http\Controllers\Account;

use App\Country;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['city', 'city.region', 'city.region.country']);
        $data = [
            'meta_title' => 'Мой профиль',
            'user' => $user,
            'cityName' => $user->city ? $user->city->region->country->name . ', ' . $user->city->region->name . ', ' . $user->city->name : null
        ];

        return view('front.account.index', $data);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $this->validateForm($request, $user->id);

        $except = [
            'username',
            'email'
        ];

        if($request->telephone && !$user->hasTelephone($request->input('telephone_code'), $request->input('telephone'))) {
            $user->telephones()->create($request->merge([
                'main' => 1
            ])->except(['telephone_confirmed']));
        }

        $user->update($request->except($except));

        return redirect()->route('account.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserRole(Request $request)
    {
        $json['success'] = false;

        if(Auth::user()->getRoles()->isNotEmpty()) {
            return Response::json($json);
        }

        $v = \Validator::make($request->all(), [
            'role_id' => 'required|numeric|allowed_role_id'
        ]);

        if($v->fails()) {
            $json['errors'] = $v->errors();
            return Response::json($json);
        }

        Auth::user()->attachRole($request->input('role_id'));
        $json['success'] = true;

        return Response::json($json);
    }

    public function noAccessSection()
    {
        $data = [
            'meta_title' => 'Доступ запрещен',
            'user' => Auth::user()
        ];

        return view('front.account.no-access-section', $data);
    }

    private function validateForm(Request $request, int $user_id = null)
    {
        $v = \Validator::make($request->all(), [
            'name' => 'sometimes|required|max:255',
            'password' => 'sometimes|required',
            'city_id' => 'sometimes|required|numeric',
            'telephone' => [
                'sometimes',
                'required',
                'telephone',
                'size:10',
                Rule::unique('user_telephones', 'telephone')->ignore($user_id, 'user_id')
            ],
            'telephone_code' => 'sometimes|required'
        ]);

        $v->sometimes('current_password', ['required', 'hash_check:'.Auth::user()->password], function ($input) {
            return !empty($input->current_password) || !empty($input->password);
        });

        $v->validate();
    }
}
