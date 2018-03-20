<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ExcelUploadHandler;
use App\Http\Requests\Api\ExcelRequest;
use App\Jobs\ImportExcel;
use App\Models\Job;

class ExcelsController extends Controller
{
    public function store(ExcelRequest $request, ExcelUploadHandler $uploadHandler)
    {
        //上传Excel文件
        $result = $uploadHandler->save($request->excel, str_plural($request->type), $request->type);

        //新建任务记录
        $job = new Job();
        switch ($request->type) {
            case 'init':
                $job->type = config('enums.excel_type.init');
                break;
        }
        $job->status = config('enums.job_status.init');
        $job->save();

        //把导入Excel的放到队列
        dispatch(new ImportExcel($result['path'], $job));

        return $this->response->noContent()->setStatusCode(201);
    }
}
