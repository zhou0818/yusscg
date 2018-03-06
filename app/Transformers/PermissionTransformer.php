<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2018/2/8
 * Time: 17:48
 */

namespace App\Transformers;

use Spatie\Permission\Models\Permission;
use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{
    public function transform(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'name' => trans('permissions.' . $permission->name)
        ];
    }
}