<?php

namespace App\Http\Controllers\Account;

use App\Profile;
use App\ProfileComment;
use App\Traits\AssessmentsTrait;
use App\Traits\HiddenTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ProfileCommentController extends Controller
{
    use AssessmentsTrait;
    use HiddenTrait;

    public function store(Request $request, Profile $profile, ProfileComment $profileComment = null)
    {
        $this->validateForm($request);
        $comment = Auth::user()->profile_comments()->create($request->merge([
            'profile_id' => $profile->id,
            'parent_id' => $profileComment->id ?? null
        ])->all());

        $json['success'] = true;
        $json['id'] = $comment->id;
        $json['view'] = view('partials.profile.partials.comment', ['comment' => $comment, 'profile' => $profile])->render();

        return Response::json($json);
    }

    public function assessment(Request $request, ProfileComment $profileComment)
    {
        return $this->toggleAssessment($request, $profileComment);
    }

    public function hide(ProfileComment $profileComment)
    {
        return $this->toggleHide($profileComment);
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'comment' => 'required|string'
        ]);

        return $v->validate();
    }
}
