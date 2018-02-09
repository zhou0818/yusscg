<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Transformers\PermissionTransformer;

class PermissionsController extends Controller
{
    public function rolesStore(User $user)
    {
        $user->assignRole('Admin');
        return $this->response->noContent();
    }

    public function permissionsShow()
    {
        return $this->response->collection(Permission::all(), new PermissionTransformer());
    }
}
