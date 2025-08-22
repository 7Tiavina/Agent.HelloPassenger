<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Client; // Import the Client model

class ClientController extends Controller
{
    public function updateProfile(Request $request)
    {
        // Use the 'client' guard to get the authenticated client
        $client = Auth::guard('client')->user();

        if (!$client) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'telephone' => 'nullable|string|max:255',
                'civilite' => 'nullable|string|max:255',
                'nomSociete' => 'nullable|string|max:255',
                'adresse' => 'nullable|string|max:255',
                'complementAdresse' => 'nullable|string|max:255',
                'ville' => 'nullable|string|max:255',
                'codePostal' => 'nullable|string|digits:5', // Enforce 5 digits
                'pays' => 'nullable|string|max:255',
            ]);

            // Update client data
            $client->fill($validatedData);
            $client->save();

            // Explicitly refresh the authenticated user's instance in the session
            $client->refresh(); // Refresh the model instance from the database
            Auth::guard('client')->setUser($client); // Update the authenticated user in the session

            Log::info('Client profile updated successfully for client: ' . $client->id);
            return response()->json(['message' => 'Profile updated successfully!', 'client' => $client]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating client profile: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating client profile: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
