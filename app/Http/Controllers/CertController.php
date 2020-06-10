<?php

namespace App\Http\Controllers;

use App\Facades\Domain;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class CertController extends Controller
{
    public function index()
    {
        try {
            $robotsFile = File::get(public_path('47993071F83232DEC2448640793DBDE5.txt'));
        } catch (FileNotFoundException $e) {
            \Log::warning('File robots example wasn\'t found');
            return abort(404);
        }

        $prefix = Domain::getSubDomainPrefix();
        $domain = Domain::subDomainUrl($prefix);

        echo $robotsFile;

        die();
    }
}
