<?php
// app/Http/Controllers/BagageConsigneController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Str;

class BagageConsigneController extends Controller
{
    // Liste toutes les réservations
    public function reservList()
    {
        $reservations = Reservation::with('user')->get();
        return view('components.reservations', compact('reservations'));
    }

    // Affiche le formulaire de création
    public function create()
    {
        return view('components.reservation_create');
    }
    
    public function showByRef($ref)
        {
            // Recherche la réservation ET l’utilisateur lié
            $reservation = Reservation::where('ref', $ref)
                                      ->with('user')
                                      ->firstOrFail();

            // Affiche resources/views/components/reservation_show.blade.php
            return view('components.reservation_show', compact('reservation'));
        }
}
