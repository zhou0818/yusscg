<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2018/3/20
 * Time: 16:47
 */

namespace App\Handlers;

use App\Models\NewStudent;
use DB;
use Excel;

class ExcelImportHandler
{
    public function import($path, $type)
    {
        switch ($type) {
            //类型为初始信息
            case config('enums.excel_type.init'):
                $this->init($path);
                break;
        }

    }

    private function init($path)
    {
        //分块导入
        Excel::filter('chunk')->load($path)->chunk(200, function ($results) {
            $info_array = $results->toArray();

            //事务，执行3次
            DB::transaction(function () use ($info_array) {
                foreach ($info_array as $key => $info) {
                    NewStudent::firstOrCreate(['reg_num' => $info['报名号']],
                        [
                            'name' => $info['姓名'],
                            'reg_num' => $info['报名号'],
                            'password' => bcrypt($info['密码']),
                            'info' => json_encode(array_except($info, ['姓名', '报名号', '密码']), JSON_UNESCAPED_UNICODE)
                        ]
                    );
                }
            }, 3);
        });
    }
}