<?php

namespace App\Http\Controllers\Admin;

use App\FAQCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class FAQCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Категории FAQ',
            'categories' => FAQCategory::paginate(25)
        ];

        return view('admin.faq_categories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Новая категория'
        ];

        return view('admin.faq_categories.create', $data);
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
        FAQCategory::create($request->all());

        return redirect()->route('admin.faq.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param FAQCategory $category
     * @return \Illuminate\Http\Response
     */
    public function edit(FAQCategory $category)
    {
        $data = [
            'title' => 'Редактирование категории',
            'FAQCategory' => $category
        ];

        return view('admin.faq_categories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param FAQCategory $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, FAQCategory $category)
    {
        $this->validateForm($request);
        $category->update($request->all());

        return redirect()->route('admin.faq.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FAQCategory $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FAQCategory $category)
    {
        $json['success'] = false;
        try {
            $category->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can\'t remove FAQ Category', ['message' => $e->getMessage(), 'FAQCategory' => $category]);
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);
    }
}
