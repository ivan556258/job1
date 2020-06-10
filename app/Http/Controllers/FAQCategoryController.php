<?php

namespace App\Http\Controllers;

use App\FAQCategory;

class FAQCategoryController extends Controller
{
    public function index(FAQCategory $category)
    {
        $data = [
            'meta_title' => 'Помощь - ' . $category->name,
            'category' => $category,
            'questions' => $category->questions
        ];

        return view('front.faq.category', $data);
    }
}
