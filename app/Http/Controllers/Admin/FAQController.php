<?php

namespace App\Http\Controllers\Admin;

use App\FAQ;
use App\FAQCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Вопросы FAQ',
            'questions' => FAQ::paginate(25)
        ];

        return view('admin.faq.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'Добавить вопрос',
            'categories' => FAQCategory::all()
        ];

        return view('admin.faq.create', $data);
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
        FAQ::create($request->all());

        return redirect()->route('admin.faq.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param FAQ $faq
     * @return \Illuminate\Http\Response
     */
    public function edit(FAQ $faq)
    {
        $data = [
            'title' => 'Редактирование вопроса',
            'categories' => FAQCategory::all(),
            'question' => $faq
        ];

        return view('admin.faq.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param FAQ $faq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FAQ $faq)
    {
        $this->validateForm($request);
        $faq->update($request->all());

        return redirect()->route('admin.faq.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FAQ $faq
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FAQ $faq)
    {
        $json['success'] = false;
        try {
            $faq->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can\'t remove FAQ\'s question', ['message' => $e->getMessage(), 'faq' => $faq]);
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|max:255',
            'answer' => 'required',
            'f_a_q_category_id' => 'required'
        ]);
    }
}
