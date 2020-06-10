<?php

namespace App\Http\Controllers;

use App\FAQ;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function index()
    {
        $data = [
            'meta_title' => 'Помощь',
            'questions' => FAQ::with(['category'])->get()
        ];

        return view('front.faq.index', $data);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if(!$search) {
            return redirect()->route('faq.index');
        }

        $data = [
            'meta_title' => 'Поиск по помощи',
            'filterSearch' => $search,
            'questions' => FAQ::search($search)->with('category')->get()
        ];

        return view('front.faq.index', $data);
    }
}
