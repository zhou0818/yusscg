<?php

namespace App\Http\Requests\Api;


class UserRoleRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'roles' => 'required|array',
        ];
    }

    public function attributes()
    {
        return [
            'roles' => '角色',
        ];
    }
}
