<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Profile;
use App\Review;
use App\Traits\AssessmentsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ReviewController extends Controller
{
    use AssessmentsTrait;

    public function index()
    {
        $data = [
            'meta_title' => 'Отзывы',
            'reviews' => Auth::user()->reviews()->with(['profile'])->paginate(25)
        ];

        return view('front.account.reviews.index', $data);
    }

    public function create(Request $request, Profile $profile)
    {
        $this->validateForm($request);
        Auth::user()->reviews()->create($request->merge([
            'profile_id' => $profile->id,

        ])->all());

        return redirect()->back()->with([
            'status' => 'success',
            'message' => 'Ваш отзыв успешно отправлен. Он будет опубликован сразу после проверки администратором'
        ]);
    }

    public function assessment(Request $request, Review $review)
    {
        return $this->toggleAssessment($request, $review);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'review' => 'required',
            'rating_general' => 'required|less_then:6',
            'rating_skill' => 'required|less_then:6',
            'rating_price' => 'required|less_then:6',
            'rating_place' => 'required|less_then:6',
            'rating_service' => 'required|less_then:6',
            'rating_photo_matching' => 'required|less_then:6',
        ]);
    }
}
