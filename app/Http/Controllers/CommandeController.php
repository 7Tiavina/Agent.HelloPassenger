<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande; // N'oubliez pas d'importer le modèle Commande

class CommandeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $commandes = Commande::where('user_id', $user->id)->latest()->get();

        return view('mes-reservations', compact('commandes'));
    }
}
