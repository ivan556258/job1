<?php

namespace App\Services;

use App\Profile;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\ProfileStatistic as ProfileStatisticModel;

class ProfileStatistic
{
    private $statisticPath = 'statistic/profileStatistic';

    //Сохраняет во временный файл новый просмотр
    public function insert(Request $request, Profile $profile)
    {
        Storage::disk('local')->append($this->statisticPath,
            serialize([
                'profile_id' => $profile->id,
                'fingerprint' => $request->fingerprint(),
                'referer' => $request->server('HTTP_REFERER'),
                'created_at' => Carbon::now()
            ]));
    }

    //Вернет файл в котором хранятся просмотры еще не внесенные в БД
    public function get()
    {
        $statistic = Storage::disk('local')->path($this->statisticPath);
        if (!is_file($statistic)) {
            \Log::warning('Statistic\'s file is not found');
            return false;
        }

        return $statistic;
    }

    //Очистка файла в котором хранятся просмотры еще не внесенные в БД
    public function clear()
    {
        Storage::disk('local')->put($this->statisticPath, '');
    }

    //Сохраняет в БД данные о просмотрах
    public function insertToDB()
    {
        if($statistic = $this->get()) {
            $visitors = file($statistic, FILE_SKIP_EMPTY_LINES);
            foreach ($visitors as $visitor) {
                if(@unserialize($visitor) !== false) {
                    try {
                        ProfileStatisticModel::create(unserialize($visitor));
                    } catch (QueryException $e) {
                        \Log::warning('Can\'t insert profile statistic', ['message' => $e->getMessage(), 'visitor' => $visitor]);
                    }
                }
            }

            $this->clear();
        }
    }
}