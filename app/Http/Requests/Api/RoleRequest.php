<?php

namespace App\Http\Requests\Api;


class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'PATCH':
                return [
                    'permissions' => 'required|array',
                ];
                break;
            default:
                return [
                    'name' => 'required|min:2|unique:roles',
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'name' => '角色名',
            'permissions' => '系统功能',
        ];
    }
}
