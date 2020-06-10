<?php

namespace App\Http\Controllers\Account;

use App\Facades\ProfileService;
use App\Facades\SalonAdvertising;
use App\Http\Controllers\Controller;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
{
    protected $updateRedirect = 'account.profiles.index';
    protected $editBlade = 'front.account.profiles.edit';

    public function __construct()
    {
        $this->middleware('profileMaxCount', ['only' => ['create', 'store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $profiles = ProfileService::applyFilters($request, Auth::user()->profiles()->with(['user', 'images']));
        $profilesTotalCount = Auth::user()->profiles()->count();
        $userRole = Auth::user()->getRole();

        $data = [
            'meta_title' => 'Анкеты',
            'profiles' => $profiles->paginate(25),
            'userRole' => $userRole,
            'accessAddProfile' => !setting($userRole->slug . '.max_profiles', 'counts') || setting($userRole->slug . '.max_profiles', 'counts') > $profilesTotalCount
        ];

        return view('front.account.profiles.index', array_merge(ProfileService::getFiltersVariables($request), $data));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'meta_title' => 'Добавление анкеты',
        ];

        return view('front.account.profiles.create', array_merge($data, ProfileService::getFormVariables()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        $profile = $this->getUser($request)->profile()->create($request->all());
        ProfileService::createExtraData($request, $profile);

        return $this->storeRedirect($profile);
    }

    /**
     * @param Request $request
     * @param Profile $profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preview(Request $request, Profile $profile)
    {
        $data = ProfileService::getPreviewVariables($profile->load(config('relations.profiles.show')));
        return view('front.profiles.show', $data);
    }

    public function updateFromPreview(Request $request, Profile $profile)
    {
        $profile->active = $request->input('active');
        $profile->save();

        return redirect()->route('account.profiles.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param \App\Profile $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Profile $profile)
    {
        $data = [
            'meta_title' => 'Редактирование анкеты',
            'profile' => $profile->load(config('relations.profiles.edit'))
        ];

        return view($this->editBlade, array_merge($data, ProfileService::getFormVariables($request, $profile)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Profile $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        $this->validateForm($request);
        $profile->update($request->all());
        ProfileService::createExtraData($request, $profile);
        
        return redirect()->route($this->updateRedirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Profile $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Profile $profile)
    {
        $json['success'] = true;

        try {
            $profile->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can not delete profile', ['message' => $e->getMessage(), 'profile' => $profile]);
        }

        return Response::json($json);
    }

    public function toggleStatus(Profile $profile)
    {
        $json['success'] = false;

        $profile->update([
            'active' => !$profile->active
        ]);

        $json['success'] = true;
        $json['status'] = $profile->active;

        return Response::json($json);
    }

    public function disableAdvertising(Profile $profile)
    {
        $json['success'] = false;
        if ($profile->advertising) {
            $json['success'] = true;
            $profile->advertising()->delete();
            $json['status'] = $profile->advertising()->count();
        }

        return Response::json($json);
    }

    public function favorite(Request $request, Profile $profile)
    {
        $json['success'] = false;
        $user = Auth::user();

        if($request->input('list')) {
            $user->setList($request->input('list'));
        }

        $user->toggleFavorite($profile);
        $json['success'] = true;
        $json['isInList'] = $user->isFavorited($profile);

        return Response::json($json);
    }

    protected function getUser(Request $request): User
    {
        return Auth::user();
    }

    /**
     * @param $data
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function storeRedirect($data = null)
    {
        return redirect()->route('account.profiles.preview', $data);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'sex' => 'required',
            'experience' => 'required',
            'address' => 'sometimes|required|max:255',
            'birth' => 'required',
            'description' => 'required|string',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:255',
            'meta_keywords' => 'nullable|max:255',
            //'images' => 'required',
            'images.*.image' => 'required',
            'mans_age_to' => 'nullable|more_then:' . $request->input('mans_age_from'),
            'women_age_to' => 'nullable|more_then:' . $request->input('women_age_from'),
            'children_age_to' => 'nullable|more_then:' . $request->input('children_age_from'),
            'prices.*.*.price' => 'nullable|numeric',
            'services.*.price' => 'nullable|string|max:255',
            'advertising.*.cost' => 'required|numeric',
            'advertising' => 'count_in_array:cost,1,1',
            'metros' => 'count_in_request:metro_id,0,3',
            'user_id' => 'sometimes|required|integer',
            'area' => 'nullable|string|max:255',
            'telephone' => 'sometimes|required|telephone',
            'city_id' => 'required'
        ]);
    }
}
