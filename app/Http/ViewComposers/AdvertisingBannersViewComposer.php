<?php


namespace App\Http\ViewComposers;


use App\AdvertisingBanner;
use App\Facades\SalonAdvertising;
use App\Scopes\SlideshowScope;
use App\Slideshow;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class AdvertisingBannersViewComposer
{
    public function compose(View $view)
    {
        $data = $view->getData();

        if(empty($data['slideshowTop'])) {
            Slideshow::addGlobalScope(new SlideshowScope());
            $slideshows = Slideshow::with(['images'])->get();
            $slides = Arr::flatten(data_get($slideshows, '*.images'));
            shuffle($slides);

            $view->with('slidesTop', $slides);
        }

        if(empty($data['bannersTop'])) {
            $view->with('bannersTop', SalonAdvertising::getBanners());
        }

        if(empty($data['bannersTopExample'])) {
            $bannersTopExample[] = new AdvertisingBanner([
                'image' => 'images/banners/banner-example-0.jpeg',
                'description' => 'Салон массажа в Москве. Специалисты из Тая и Бали',
                'title' => 'Спа релакс',
                'link' => 'http://www.thaibalispa.ru/?yclid=2890998524337226854'
            ]);

            $bannersTopExample[] = new AdvertisingBanner([
                'image' => 'images/banners/banner-example-2.gif',
                'description' => 'Салон Эротического массажа',
                'title' => 'Спа-сказка',
                'link' => 'http://spaskazka.ru/ru'
            ]);

            $bannersTopExample[] = new AdvertisingBanner([
                'image' => 'images/banners/banner-example-1.jpeg',
                'description' => 'Высокая зарплата в Москве',
                'title' => 'Работа массажисткам',
                'link' => 'https://larikon.com/'
            ]);

            $view->with('bannersTopExample', $bannersTopExample);
        }
    }
}