<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2018/2/6
 * Time: 14:29
 */

namespace App\Transformers;

use App\Models\NewStudent;
use League\Fractal\TransformerAbstract;

class NewStudentTransformer extends TransformerAbstract
{
    public function transform(NewStudent $new_student)
    {
        return [
            'id' => $new_student->id,
            'name' => $new_student->name,
            'reg_num' => $new_student->reg_num,
            'is_fill' => $new_student->is_fill ? true : false,
            'is_confirm' => $new_student->is_confirm ? true : false,
            'is_lottery' => $new_student->is_lottery ? true : false,
            'is_admit' => $new_student->is_admit ? true : false,
            'info' => $new_student->info,
            'created_at' => $new_student->created_at->toDateTimeString(),
            'updated_at' => $new_student->updated_at->toDateTimeString(),
        ];
    }
}