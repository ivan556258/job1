<?php
namespace App\Traits;

use App\Translation;
use Illuminate\Support\Facades\Cache;

trait TranslationTrait
{
    //Получает перевод по домену
    public function getTranslation(string $type, string $key = null, int $type_id = null, int $city_id = null, int $country_id = null, $default = null, bool $useSession = true, bool $useCache = true)
    {
        if(!$city_id && !$country_id && $useSession) {
            try {
                $city_id = session('localisation')['city']->id;
                $country_id = session('localisation')['city']->region->country_id;
            } catch (\ErrorException $e) {
                \Log::warning('Error with localisation', ['localisation' => session('localisation')]);
            }
        }

        if($useCache && Cache::has('translations.'.$type.'.'.$key.'.'.$city_id.'.'.$country_id.'.'.$type_id)) {
            return Cache::get('translations.'.$type.'.'.$key.'.'.$city_id.'.'.$country_id.'.'.$type_id);
        }

        $translation = $this->getTranslationModel($type, $key, $type_id, $city_id, $country_id)->first();

        if($translation) {
            if(@unserialize($translation->svalue) === false) {
                $result = $translation->svalue;
            } else {
                $result = unserialize($translation->svalue);
            }
        } else {
            return $default;
        }

        if($useCache) {
            Cache::put('translations.'.$type.'.'.$key.'.'.$city_id.'.'.$country_id.'.'.$type_id, $result ?? '', 3600);
        }

        return $result;
    }

    //Обновляем перевод
    public function insertTranslation(array $translations, int $type_id = null, int $city_id = null, int $country_id = null)
    {
        foreach ($translations as $type => $translation) {
            foreach ($translation as $key => $item) {
                Translation::updateOrCreate([
                    'stype' => $type,
                    'skey' => $key,
                    'city_id' => $city_id,
                    'country_id' => $country_id,
                    'stype_id' => $type_id
                ], [
                    'stype' => $type,
                    'skey' => $key,
                    'svalue' => is_array($item) ? serialize($item) : $item,
                    'city_id' => $city_id,
                    'country_id' => $country_id,
                    'stype_id' => $type_id
                ]);
            }
        }
    }

    //Получаем билдер перевода
    public function getTranslationModel(string $type, string $key = null, int $type_id = null, int $city_id = null, int $country_id = null)
    {
        if(!$city_id && !$country_id && !$type_id) {
            return collect();
        }

        $translation = Translation::where('stype', $type)->where('stype_id', $type_id);

        if($city_id) {
            $translationCopy = Translation::where('stype', $type)->where('city_id', $city_id)->where('stype_id', $type_id);
        }

        if($key && isset($translationCopy)) {
            $translationCopy->where('skey', $key);
        }

        if(isset($translationCopy)) {
            $translationCopy = $translationCopy->first();
        }

        //Если у города нет перевода - ищем перевод по стране
        if((empty($translationCopy) || empty($translationCopy->svalue)) && $country_id) {
            $translation = $translation->where('country_id', $country_id)->whereNull('city_id');
        } else {
            $translation = $translation->where('city_id', $city_id);
        }

        if($key) {
            $translation->where('skey', $key);
        }

        return $translation;
    }
}