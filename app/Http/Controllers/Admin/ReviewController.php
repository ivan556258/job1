<?php

namespace App\Http\Controllers\Admin;

use App\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Отзывы',
            'reviews' => Review::paginate(25)
        ];

        return view('admin.reviews.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Добавить отзыв'
        ];

        return view('admin.reviews.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);
        $review = Review::create($request->all());
        $this->answer($request, $review);

        return redirect()->route('admin.reviews.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        $data = [
            'title' => 'Редактирование отзыва',
            'review' => $review
        ];

        return view('admin.reviews.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        $this->validateForm($request);
        $review->update($request->all());
        $this->answer($request, $review);

        return redirect()->route('admin.reviews.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Review $review)
    {
        $json['success'] = false;
        try {
            $review->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove review', ['message' => $e->getMessage(), 'review' => $review]);
        }

        return Response::json($json);
    }

    private function answer(Request $request, Review $review)
    {
        if(empty($request->input('answer.answer'))) {
            $review->answer()->delete();
        } else {
            $review->answer()->updateOrCreate([], $request->input('answer'));
        }
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'review' => 'required',
            'rating_general' => 'required|less_then:6',
            'rating_skill' => 'required|less_then:6',
            'rating_price' => 'required|less_then:6',
            'rating_place' => 'required|less_then:6',
            'rating_service' => 'required|less_then:6',
            'rating_photo_matching' => 'required|less_then:6',
            'profile_id' => 'required',
            'user_id' => 'required'
        ]);

        $v->sometimes(['answer.user_id', 'answer.answer'], ['required'], function ($input) {
            return !empty($input->answer['answer']);
        });

        $v->validate();
    }
}
