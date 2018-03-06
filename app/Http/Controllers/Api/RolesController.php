<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RoleRequest;
use App\Transformers\RoleTransformer;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function show()
    {
        return $this->response->collection(Role::all(), new RoleTransformer());
    }

    public function store(RoleRequest $request, Role $role)
    {
        $role->name = $request->name;
        $role->save();
        return $this->response->item($role, new RoleTransformer())
            ->setStatusCode(201);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->name = $request->name;
        $role->save();
        return $this->response->item($role, new RoleTransformer());
    }

    public function destroy(Role $role)
    {
        $role->permissions()->detach();
        $role->delete();
        return $this->response->noContent();
    }

    public function syncPermissions(RoleRequest $request, Role $role)
    {
        $permissions = $request->permissions;
        $role->syncPermissions($permissions);
        return $this->response->item($role, new RoleTransformer());
    }
}
