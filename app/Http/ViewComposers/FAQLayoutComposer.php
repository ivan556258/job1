<?php
namespace App\Http\ViewComposers;


use App\FAQCategory;
use Illuminate\View\View;

class FAQLayoutComposer
{
    public function compose(View $view)
    {
        $view->with('navCategories', FAQCategory::all());
    }
}