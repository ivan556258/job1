<?php

namespace App\Console\Commands;

use App\City;
use Illuminate\Console\Command;

class CitiesPrefixTranslator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:city-prefix-translate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate city name to domain prefix';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cities = City::wherePrefix('prefix')->get();

        foreach ($cities as $city) {
            $city->update([
                'prefix' => $this->translate($city->name)
            ]);
        }
    }

    private function translate(string $name)
    {
        $name = (string) $name;
        $name = strip_tags($name);
        $name = str_replace(array("\n", "\r"), " ", $name);
        $name = preg_replace("/\s+/", ' ', $name);
        $name = trim($name);
        $name = function_exists('mb_strtolower') ? mb_strtolower($name) : strtolower($name); // переводим строку в нижний регистр (иногда надо задать локаль)
        $name = strtr($name, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $name = preg_replace("/[^0-9a-z-_ ]/i", "", $name);
        $name = str_replace(" ", "-", $name);

        return $name;
    }
}
