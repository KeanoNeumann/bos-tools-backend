<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganisationController extends Controller
{
    public function allAdmin(Request $reqest)
    {
        if (Auth::user()->tenant_admin) {
            return Organisation::all();
        }

        return Auth::user()->organisations()->wherePivot("is_admin", true)->get();
    }


    public function delete(Request $request, Organisation $organisation)
    {
        if (!Auth::user()->tenant_admin) {
            return response()->json(['error' => 'Unauthorized. Only tenant admins can delete organisations.'], 403);
        }

        $organisation->delete();
        return "OK";
    }


    public function create(Request $request)
    {
        if (!Auth::user()->tenant_admin) {
            return response()->json(['error' => 'Unauthorized. Only tenant admins can create organisations.'], 403);
        }

        $validatedData = $request->validate(Organisation::getValidationRules());

        $validatedData['active'] = $validatedData['active'] ?? true;
        $validatedData['tenant_id'] = Auth::user()->tenant_id;

        try {
            $organisation = Organisation::create($validatedData);

            Auth::user()->organisations()->attach($organisation->id, ['is_admin' => true]);

            return response()->json([
                'message' => 'Organisation successfully created',
                'organisation' => $organisation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the organisation',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, Organisation $organisation)
    {
        $organisationIds = Auth::user()->organisations()->wherePivot("is_admin", true)->get()->pluck("id")->toArray();
        if (!Auth::user()->tenant_admin && !in_array($organisation->id, $organisationIds)) {
            return response()->json(['message' => 'Unauthorized. Only tenant admins can update organisations.'], 403);
        }

        if ($organisation->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['message' => 'Unauthorized. You can only update organisations in your own tenant.'], 403);
        }

        $validationRules = Organisation::getValidationRules();

        unset($validationRules['tenant_id']);

        $validatedData = $request->validate($validationRules);

        try {
            $organisation->update($validatedData);

            return response()->json([
                'message' => 'Organisation successfully updated',
                'organisation' => $organisation->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the organisation',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function groups(Request $request, Organisation $organisation)
    {
        return $organisation->groups;
    }
}
