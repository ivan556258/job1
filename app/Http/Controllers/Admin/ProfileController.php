<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ProfileService;
use App\Http\Controllers\Account\ProfileController as ProfileControllerParent;
use App\Profile;
use App\Scopes\ProfilesActiveScope;
use App\Scopes\ProfilesBlockScope;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class ProfileController extends ProfileControllerParent
{
    protected $updateRedirect = 'admin.profiles.index';
    protected $editBlade = 'admin.profiles.edit';

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $profiles = ProfileService::applyFilters($request, Profile::with(['image', 'images']));

        $data = [
            'title' => 'Профили',
            'profiles' => $profiles->paginate(25)->appends(Input::except('page'))
        ];

        return view('admin.profiles.index', array_merge($data, ProfileService::getFiltersVariables($request)));
    }

    public function create()
    {
        $data = [
            'title' => 'Создать профиль'
        ];

        return view('admin.profiles.create', array_merge($data, ProfileService::getFormVariables()));
    }

    /**
     * @param Profile $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function block(Profile $profile)
    {
        $json['success'] = false;

        $profile->update([
            'block' => !$profile->block
        ]);

        $json['success'] = true;
        $json['block'] = $profile->block;

        return Response::json($json);
    }

    public function search(Request $request)
    {
        $json['success'] = false;
        if($request->has('profile')) {
            $json['profiles'] = Profile::search($request->input('profile'))->with('user')
                ->withGlobalScope('ProfileActiveScope', new ProfilesActiveScope)
                ->withGlobalScope('ProfileBlockScope', new ProfilesBlockScope)
                ->limit(5)->get();
            $json['success'] = true;
        }

        return Response::json($json);
    }

    public function getProfilesByUser(User $user)
    {
        $json['profiles'] = $user->hasRole('individual') ? $user->profiles : false;

        return Response::json($json);
    }

    protected function getUser(Request $request): User
    {
        if($request->input('user_id')) {
            return User::whereKey($request->input('user_id'))->firstOrFail();
        }

        return Auth::user();
    }

    protected function storeRedirect($data = null)
    {
        return redirect()->route('admin.profiles.index');
    }
}
