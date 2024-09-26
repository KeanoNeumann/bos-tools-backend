<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate(Group::getValidationRules());

        $organisationIds = Auth::user()->organisations()->wherePivot("is_admin", true)->get()->pluck("id")->toArray();
        if (!Auth::user()->tenant_admin && !in_array($validatedData["organisation_id"], $organisationIds)) {
            return response()->json(['message' => 'Unauthorized. Only tenant admins can update groups.'], 403);
        }


        try {
            $organisation = Group::create($validatedData);

            return response()->json([
                'message' => 'Group successfully created',
                'group' => $organisation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the group',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, Group $group)
    {
        $validatedData = $request->validate(Group::getValidationRules());

        $organisationIds = Auth::user()->organisations()->wherePivot("is_admin", true)->get()->pluck("id")->toArray();
        if (!Auth::user()->tenant_admin && !in_array($validatedData["organisation_id"], $organisationIds)) {
            return response()->json(['message' => 'Unauthorized. Only tenant admins can update groups.'], 403);
        }

        $validatedData = $request->validate(Group::getValidationRules());

        try {
            $group->update($validatedData);

            return response()->json([
                'message' => 'Group successfully updated',
                'group' => $group->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the groups',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function delete(Request $request, Group $group)
    {
        $organisationIds = Auth::user()->organisations()->wherePivot("is_admin", true)->get()->pluck("id")->toArray();
        if (!Auth::user()->tenant_admin && !in_array($group->organisation?->id, $organisationIds)) {
            return response()->json(['message' => 'Unauthorized. Only tenant admins can update groups.'], 403);
        }

        $group->delete();
        return "OK";
    }
}
