<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function acceuil()
    {
        return view('Front.acceuil');
    }

    public function redirectForm()
    {
        return view('Front.formulaire-consigne');
    }
}
