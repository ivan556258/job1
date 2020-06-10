<?php

namespace App\Http\Controllers;

use App\Facades\Domain;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class RobotsController extends Controller
{
    public function index()
    {
        try {
            $robotsFile = File::get(public_path('robots-example.txt'));
        } catch (FileNotFoundException $e) {
            \Log::warning('File robots example wasn\'t found');
            return abort(404);
        }

        $prefix = Domain::getSubDomainPrefix();
        $domain = Domain::subDomainUrl($prefix);
        $siteMapFile = $prefix ? $prefix . '.sitemap.xml' : 'sitemap.xml';

        $robots = $robotsFile . "\n" . 'Host: ' . $domain . "\n" . 'Sitemap: ' . $domain . '/' . config('general.siteMapFolder') . '/' . $siteMapFile;

        echo $robots;

        die();
    }
}
