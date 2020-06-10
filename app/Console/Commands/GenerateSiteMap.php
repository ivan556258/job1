<?php

namespace App\Console\Commands;

use App\City;
use App\Facades\Domain;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSiteMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:sitemap {--city_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Site map generation';

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
        $city_id = $this->option('city_id');
        $this->generate($city_id);
    }

    private function generate($city = null, $siteMap = null)
    {
        set_time_limit(0);

        $siteMapsFolder = config('general.siteMapFolder') . '/';

        if(!$siteMap) {
            $siteMap = SitemapGenerator::create(config('app.url'))
                ->hasCrawled(function (Url $url) {
                    if($url->segment(1) === 'profiles' || $url->segment(1) === 'salons') {
                        return;
                    }

                    return $url;
                })->getSitemap();
        }

        if($city) {
            if(!$city instanceof City) {
                $city = City::whereKey($city)->firstOrFail();
            }

            $siteMapCopy = Sitemap::create();

            $siteMapFile = $city->prefix ? $city->prefix.'.sitemap.xml' : 'sitemap.xml';

            resolve('url')->forceRootUrl(Domain::subDomainUrl($city->prefix));
            $city->profiles->each(function ($value, $key) use ($siteMapCopy) {
                $siteMapCopy->add(route('profiles.show', $value));
            });

            $city->salons->each(function ($value, $key) use ($siteMapCopy) {
                $siteMapCopy->add(route('salons.show', $value));
            });

            foreach ($siteMap->getTags() as $tag) {
                $siteMapCopy->add($tag->setUrl(Domain::subDomainUrl($city->prefix, $tag->path())));
            }

            $siteMapCopy->writeToFile(public_path($siteMapsFolder . $siteMapFile));
        } else {
            $cities = City::with(['profiles', 'salons'])->get();
            foreach ($cities as $city) {
                $this->generate($city, $siteMap);
            }
        }
    }
}
