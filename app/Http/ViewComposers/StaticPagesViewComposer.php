<?php


namespace App\Http\ViewComposers;


use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class StaticPagesViewComposer
{
    public function compose(View $view)
    {
        $view->with('meta_title', translate(Route::getCurrentRoute()->getAction('pageCode'), 'meta_title') . ' --- массаж в городе ' . getCityNameByDomain());
        $view->with('meta_description', translate(Route::getCurrentRoute()->getAction('pageCode'), 'meta_description'));
        $view->with('meta_keywords', translate(Route::getCurrentRoute()->getAction('pageCode'), 'meta_keywords'));
        $view->with('content', translate(Route::getCurrentRoute()->getAction('pageCode'), 'content'));
    }
}