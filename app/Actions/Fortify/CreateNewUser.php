<?php

namespace App\Actions\Fortify;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'invitation_token' => ['required', 'string'],
        ])->validate();

        $invitation = Invitation::where('token', $input['invitation_token'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            throw ValidationException::withMessages([
                'invitation_token' => ['Invalid or expired invitation token.'],
            ]);
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $invitation->email,
            'password' => Hash::make($input['password']),
        ]);

        $invitation->delete();

        return $user;
    }
}
