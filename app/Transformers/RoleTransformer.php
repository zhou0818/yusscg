<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2018/2/9
 * Time: 11:29
 */

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RoleTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['permissions'];

    public function transform(Role $role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
        ];
    }

    public function includePermissions(Role $role)
    {
        return $this->collection($role->permissions, new PermissionTransformer());
    }
}