<?php

namespace App\Http\Controllers;

class LegalPageController extends Controller
{
    public function privacyPolicy()
    {
        return view('legal.privacy-policy');
    }

    public function termsOfService()
    {
        return view('legal.terms-of-service');
    }

    public function returnPolicy()
    {
        return view('legal.return-policy');
    }

    public function shippingPolicy()
    {
        return view('legal.shipping-policy');
    }
}
