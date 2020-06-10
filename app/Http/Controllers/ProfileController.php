<?php

namespace App\Http\Controllers;

use App\Facades\Advertising;
use App\Facades\Domain;
use App\Facades\ProfileService;
use App\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->profiles($request, Profile::whereHas('user.roles', function ($query) {
            $query->where('roles.slug', 'individual');
        }), ['heading' => 'Частные объявления']);

        return view('front.profiles.index', $data);
    }

    public function newest(Request $request)
    {
        $data = $this->profiles($request, Profile::newest(), ['heading' => 'Новые объявления']);
        return view('front.profiles.index', $data);
    }

    public function popular(Request $request)
    {
        $data = $this->profiles($request, Profile::orderByDesc('views'), ['heading' => 'Популярные объявления']);
        return view('front.profiles.index', $data);
    }

    /**
     * @param Profile $profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Profile $profile)
    {
        $profile->load(config('relations.profiles.show'));

        $data = ProfileService::getShowVariables($profile);

        return view('front.profiles.show', $data);
    }

    /**
     * @param Profile $profile
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function reviews(Request $request, Profile $profile)
    {
        $reviews = ProfileService::reviews($profile, $request->input('with_reviews', true), $request->input('with_comments', true));
        $json = [
            'lastPage' => $reviews->lastPage(),
            'currentPage' => $reviews->currentPage(),
            'nextPageUrl' => route('profiles.loadmore.reviews', ['profile' => $profile]) . $reviews->nextPageUrl(),
            'view' => view('front.profiles.partials.reviews', ['reviews' => $reviews, 'profile' => $profile])->render()
        ];

        return Response::json($json);
    }

    private function profiles(Request $request, Builder $profiles, array $extraData = []): array
    {
        if(!$request->input('page') || $request->input('page') == 1) {
            $topProfiles = Advertising::getProfiles();
        }

        $profiles = ProfileService::applyFilters($request, $profiles->with(config('relations.girl-card')));

        if(isset($topProfiles)) {
            if($request->all()) {
                $filteredProfiles = $profiles->get();
                $topProfiles = $topProfiles->whereIn('id', data_get($filteredProfiles, '*.id'));
            }

            $profiles->whereNotIn('id', data_get($topProfiles, '*.id'));
        }

        $data = [
            'topProfiles' => $topProfiles ?? [],
            'profiles' => $profiles->paginate(20)->appends(Input::except('page'))
        ];

        return array_merge($data, $extraData);
    }
}
