<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    public function termsOfService()
    {
        return view('terms-of-service');
    }
}
