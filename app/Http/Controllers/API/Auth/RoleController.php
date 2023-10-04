<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with(['permissions'])->paginate();
        return RoleResource::collection($roles);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoleRequest $request)
    {
        $role = Role::create(['name' => $request->input('roleName')]);
        $role->syncPermissions($request->input('permissions'));
        return response()->json(["success"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        return new RoleResource($role);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'roleName' => 'required|string',
            "permissionsToAdd" => 'array|nullable',
            "permissionsToRemove" => 'array|nullable'
        ]);

        $role = Role::findOrFail($id);

        //Update the role name
        $role->name = $request->input('roleName');
        $role->save();

        //To remove  permissions
        foreach ($request->input('permissionsToRemove') as $permission) {
            $role->revokePermissionTo($permission);
        }

        //To add new permissions
        $role->syncPermissions($request->input('permissionsToAdd'));

        return response()->json(['message' => "Role updated successfully"], 200);
    }

    /**p
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}