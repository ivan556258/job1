<?php

namespace App\Http\Controllers\Account;

use App\Review;
use App\ReviewAnswer;
use App\Traits\AssessmentsTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ReviewAnswerController extends Controller
{
    use AssessmentsTrait;

    public function create(Request $request, Review $review)
    {
        $json['success'] = false;

        $v = $this->validateForm($request);
        if($v->fails()) {
            $json['errors'] = $v->errors();
            return Response::json($json);
        }

        $review->answer()->create($request->merge([
            'user_id' => Auth::id()
        ])->all());

        $json['success'] = true;
        return Response::json($json);
    }

    public function assessment(Request $request, ReviewAnswer $answer)
    {
        return $this->toggleAssessment($request, $answer);
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'answer' => 'required'
        ]);

        return $v;
    }
}
