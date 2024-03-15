<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LangController extends Controller
{
    //
    public function setLocale($locale)
    {
        $supportedLocales = ['en', 'ar'];
        if (!in_array($locale, $supportedLocales)) {
            abort(404);
        }

        session(['lang_local' => $locale]);
        return redirect()->back();
    }
}
