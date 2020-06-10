<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class Observer
{
    protected function clearCache(string $cacheName)
    {
        Cache::forget($cacheName);
    }

    protected function setCache(string $cacheName, $value, int $time = null)
    {
        if($time) {
            Cache::put($cacheName, $value, $time);
        } else {
            Cache::forever($cacheName, $value);
        }
    }
}