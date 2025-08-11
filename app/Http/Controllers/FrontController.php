<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

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


    public function showClientLogin()
    {
        return view('client.login'); // Ta vue dédiée si besoin, sinon intégrer modal existant
    }

    
    public function clientLogin(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        $client = Client::where('email', $request->email)->first();

        if ($client && Hash::check($request->password, $client->password_hash)) {
            Auth::guard('client')->login($client); // login via guard client
            $request->session()->regenerate();

            // Redirect to /link-form (route name: form-consigne)
            return redirect()->route('form-consigne');
        }

        // échec : on renvoie avec un flash pour afficher le modal d'erreur
        return back()->withInput($request->only('email'))->with('login_error', true);
    }

    public function clientLogout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('front.acceuil');
    }


    public function clientDashboard()
    {
        return view('client.dashboard', ['client' => Auth::guard('client')->user()]);
    }



}
