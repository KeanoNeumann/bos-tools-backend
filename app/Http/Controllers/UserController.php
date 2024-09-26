<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function all(Request $request)
    {
        if (!Auth::user()->tenant_admin)
            return abort(401);

        return User::all();
    }

    public function allForOrganisation(Request $request, Organisation $organisation)
    {
        return $organisation->members->load("organisations");
    }
}
