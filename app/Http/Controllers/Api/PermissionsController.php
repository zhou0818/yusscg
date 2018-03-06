<?php

namespace App\Http\Controllers\Api;

use App\Transformers\PermissionTransformer;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function show()
    {
        return $this->response->collection(Permission::all(), new PermissionTransformer());
    }
}
