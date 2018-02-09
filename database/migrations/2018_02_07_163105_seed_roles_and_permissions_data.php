<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SeedRolesAndPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 清除缓存
        app()['cache']->forget('spatie.permission.cache');

        // 先创建权限
        Permission::create(['name' => 'edit_settings']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_new_students']);
        Permission::create(['name' => 'confirm_new_students']);

        // 创建站长角色，并赋予权限
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo('edit_settings');

        //创建Admin
        $user = User::create([
            'name' => 'Admin',
            'phone' => '15808797346',
            'password' => bcrypt('123456'),
        ]);

        // 初始化用户角色，将 Admin 赋予 Admin 用户
        $user->assignRole('Admin');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 清除缓存
        app()['cache']->forget('spatie.permission.cache');

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');

        // 解除模型的批量填充限制
        Model::unguard();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        Model::reguard();

        User::where(['name' => 'Admin', 'phone' => '15808797346'])->delete();
    }
}
