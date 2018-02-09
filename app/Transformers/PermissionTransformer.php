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
        $names_cn = [
            'edit_settings' => '系统设置',
            'manage_users' => '人员管理',
            'manage_new_students' => '招生管理',
            'confirm_new_students' => '现场确认',
        ];
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'name_cn' => $names_cn[$permission->name]
        ];
    }
}