<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users,email']);

        $invitation = Invitation::create([
            'email' => $request->email,
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        // TODO: Send email invitation

        return response()->json(['message' => 'Invitation sent successfully']);
    }
}
