<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings']
], function ($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function ($api) {
        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');
        // 第三方登录
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');
        // 登录
        $api->post('{auth_type}/authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
        // 需要 token 验证的接口
        $api->group(['middleware' => ['api.auth']], function ($api) {
            // 刷新token
            $api->put('authorizations/current', 'AuthorizationsController@update')
                ->name('api.authorizations.update');
            // 删除token
            $api->delete('authorizations/current', 'AuthorizationsController@destroy')
                ->name('api.authorizations.destroy');
        });

    });

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        // 需要 token 验证的接口，普通用户
        $api->group(['middleware' => ['api.auth', 'auth.type:user']], function ($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            //查询系统功能
            $api->get('permissions', 'PermissionsController@show')
                ->name('api.permissions.show');
            //查询角色信息
            $api->get('roles', 'RolesController@show')
                ->name('api.roles.show');
            //新建角色
            $api->post('roles', 'RolesController@store')
                ->name('api.roles.store');
            //修改角色名
            $api->put('roles/{role}', 'RolesController@update')
                ->name('api.roles.update');
            //删除角色
            $api->delete('roles/{role}', 'RolesController@destroy')
                ->name('api.roles.destroy');
            //同步角色的系统功能
            $api->patch('roles/{role}', 'RolesController@syncPermissions')
                ->name('api.roles.syncPermissions');
        });
        // 需要 token 验证的接口,新生
        $api->group(['middleware' => ['api.auth', 'auth.type:new_student']], function ($api) {
            // 当前登录用户信息
            $api->get('new_student', 'NewStudentsController@me')
                ->name('api.new_student.show');
        });
    });
});
