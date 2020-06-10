<?php

namespace App\Services;

use App\Events\ImageDeleted;
use App\Events\VideoDeleted;
use App\Salon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use \App\Facades\SalonTransactions;

class SalonService
{
    //Сохраняем доп связи салона
    public function createExtraData(Request $request, Salon $salon)
    {
        //Сохраняем фото
        if ($request->has('images')) {
            foreach ($request->input('images') as $item) {
                $salon->images()->updateOrCreate([
                    'image' => $item['image']
                ], $item);
            }
        }

        //Сохраняем видео
        if ($request->has('videos')) {
            foreach ($request->input('videos') as $item) {
                $salon->videos()->updateOrCreate([
                    'video' => $item['video']
                ], $item);
            }
        }

        //Удаляем фото из БД и диска
        if ($request->input('images_deleting')) {
            foreach (explode(',', $request->input('images_deleting')) as $item) {
                $image = $salon->images()->whereKey($item);
                event(new ImageDeleted($image->first()->image));
                $image->delete();
            }
        }

        //Удаляем видео из БД и диска
        if ($request->input('videos_deleting')) {
            foreach (explode(',', $request->input('videos_deleting')) as $item) {
                $video = $salon->videos()->whereKey($item);
                event(new VideoDeleted($video->first()->video));
                $video->delete();
            }
        }

        //Синхронизируем метро
        $salon->metros()->sync($request->input('metros') ? Arr::pluck($request->input('metros'),
            ['metro_id']) : null);
    }

    //Метод ежемесячной активации салона
    public function everyMonthActivation()
    {
        $salons = Salon::whereActive(1)
            ->where('activated_at', '<=', Carbon::today()->addMonth(-1))->with(['user'])->get();

        foreach ($salons as $salon) {
            if($salon->user->actualBalance() < setting('salon.activate', 'prices')) {
                $salon->active = 0;
                $salon->save();
            } else {
                SalonTransactions::setActive($salon);
            }
        }
    }
}