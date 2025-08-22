<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Reservation;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;

use App\Models\BagageHistory;

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

    // Affiche chaque section
    public function overview()    { return view('components.overview'); }
    
    public function analytics()   { return view('components.analytics'); }
    public function chat()        { return view('components.chat'); }

    public function users() {
        $agents = User::where('role', 'agent')->get();
        $users = User::where('role', 'user')->get();
        return view('components.users', compact('agents', 'users'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|unique:users',
            'role'                  => 'required|in:user,agent',
            'password'              => 'required|min:6|confirmed',
        ]);

        User::create([
            'email'           => $request->email,
            'role'            => $request->role,
            'password_hash'   => Hash::make($request->password),
        ]);

        return redirect()->route('users')->with('success', 'Utilisateur créé.');
    }

    public function orders()
    {
        $reservations = \App\Models\Reservation::with('user')
            ->orderByDesc('created_at')
            ->take(25)
            ->get();

        return view('components.orders', compact('reservations'));
    }

   public function myorders()
    {
        $agentId = session('user_id');

        $reservationIds = BagageHistory::where('agent_id', $agentId)
            ->where('status', 'collecté')
            ->pluck('reservation_id');

        $reservations = Reservation::with(['user', 'histories.agent'])
            ->whereIn('id', $reservationIds)
            ->orderByDesc('created_at')
            ->take(25)
            ->get();

        // → On ne passe PAS 'version' du tout
        $options = new QROptions([
            'eccLevel'   => QRCode::ECC_L,
            'outputType' => QROutputInterface::MARKUP_SVG,
            'scale'      => 5,
        ]);
        $qrGenerator = new QRCode($options);

        foreach ($reservations as $res) {
            $res->qr_svg = $qrGenerator->render($res->ref);
        }

        return view('components.myorders', compact('reservations'));
    }
}