<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ExcelUploadHandler;
use App\Http\Requests\Api\ExcelRequest;
use App\Models\NewStudent;
use DB;
use Excel;

class ExcelsController extends Controller
{
    public function store(ExcelRequest $request, ExcelUploadHandler $uploadHandler)
    {
        //设置超时时间
        ini_set('max_execution_time', 7200);

        //上传Excel文件
        $result = $uploadHandler->save($request->excel, str_plural($request->type), $request->type);

        try {
            //分块导入
            Excel::filter('chunk')->load($result['path'])->chunk(200, function ($results) {
                $info_array = $results->toArray();
                DB::transaction(function () use ($info_array) {
                    foreach ($info_array as $key => $info) {
                        $this->importInfo($info);
                    }
                },5);
            });
        } catch (\Exception $e) {
            return $this->response->errorBadRequest('数据导入错误，请重试！');
        }
        return $this->response->noContent()->setStatusCode(201);
    }

    private function importInfo($info)
    {
        $new_student = NewStudent::firstOrCreate(['reg_num'=>$info['报名号']]);
        $new_student->name = $info['姓名'];
        $new_student->reg_num = $info['报名号'];
        $new_student->password = bcrypt($info['密码']);
        //从数组中删除以下元素
        $info = array_except($info, ['姓名', '报名号', '密码']);
        //转换为JSON格式
        $new_student->info = json_encode($info, JSON_UNESCAPED_UNICODE);
        $new_student->save();
    }

}
