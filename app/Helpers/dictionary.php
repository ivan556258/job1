<?php
if(!function_exists('dictionary')) {
    function dictionary(int $count, array $values)
    {
        $string = null;

        if ($count === 0) {
            return $values['zero'];
        }

        switch ($count) {
            case $count % 10 === 1:
                $string = $count . ' ' . $values['one'];
                break;
            case $count % 10 >= 2 && $count % 10 < 5:
                $string = $count . ' ' . $values['toFour'];
                break;
            case $count % 10 >= 5 || $count % 10 === 0:
                $string = $count . ' ' . $values['other'];
        }

        return $string;
    }
}
if (!function_exists('ageDictionary')) {
    function ageDictionary(int $age)
    {
        return dictionary($age, ['zero' => 'менее года', 'one' => 'год', 'toFour' => 'года', 'other' => 'лет']);
    }
}

if (!function_exists('photoDictionary')) {
    function photoDictionary(int $count)
    {
        return dictionary($count, ['zero' => '0 фотографий', 'one' => 'фотография', 'toFour' => 'фотографии', 'other' => 'фотографий']);
    }
}