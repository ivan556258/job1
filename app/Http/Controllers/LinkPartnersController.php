<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LinkPartnersController extends Controller
{

    const HTTP = "http://";
    const HTTPS = "https://";

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function index(Request $request)
    {
        $fullLink = \Request::getRequestUri();

        $link = explode("=", $fullLink);

        $pos1 = strripos($link[1], self::HTTP);
        $pos2 = strripos($link[1], self::HTTPS);

        if ($pos1 === 0 || $pos2 === 0) {
            return redirect($link[1]);
        }

        return redirect('/');
    }
}
