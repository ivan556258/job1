<?php

namespace App\Http\Controllers\Admin;

use App\ProfileComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ProfileCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Комментарии',
            'comments' => ProfileComment::with(['profile'])->paginate(25)
        ];

        return view('admin.profile-comments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Написать комментарий'
        ];

        return view('admin.profile-comments.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        ProfileComment::create($request->all());

        if($request->ajax()) {
            return Response::noContent();
        }

        return redirect()->route('admin.profile-comments.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProfileComment  $profileComment
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileComment $profileComment)
    {
        $data = [
            'title' => 'Редактирование комментария',
            'comment' => $profileComment
        ];

        return view('admin.profile-comments.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProfileComment  $profileComment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfileComment $profileComment)
    {
        $this->validateForm($request);

        $profileComment->update($request->all());
        return redirect()->route('admin.profile-comments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProfileComment  $profileComment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProfileComment $profileComment)
    {
        $json['success'] = false;

        try {
            $profileComment->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can\'t remove profile\'s comment', ['message' => $e->getMessage(), 'comment' => $profileComment]);
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required|string',
            'profile_id' => 'required',
            'user_id' => 'required'
        ]);
    }
}
