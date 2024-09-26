<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function get(Request $request, Tenant $tenant)
    {
        // if (Auth::user()->tenant_id != $tenant->id)
        //     return abort(401, "Sie sind nicht berechtigt den Tenant zu verwalten.");

        return $tenant;
    }
}
