<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Récupère par Eloquent
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password_hash)) {
            // Stocke seulement l’ID et le rôle
            session(['user_id' => $user->id, 'user_role' => $user->role]);
            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['email' => 'Email ou mot de passe incorrect'])
            ->withInput();
    }

    public function dashboard()
    {
        if (! session()->has('user_id')) {
            return redirect()->route('login');
        }
        return view('dashboard');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
