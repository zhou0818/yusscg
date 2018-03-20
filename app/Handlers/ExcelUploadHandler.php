<?php
/**
 * Created by PhpStorm.
 * User: zhou
 * Date: 2018/3/13
 * Time: 17:35
 */

namespace App\Handlers;


class ExcelUploadHandler
{
    // 只允许以下后缀名的文件上传
    protected $allowed_ext = ["xls", "xlsx"];

    public function save($file, $folder, $file_prefix)
    {
        // 构建存储的文件夹规则，值如：uploads/excels/init/201709/21/
        // 文件夹切割能让查找效率更高。
        $folder_name = "uploads/excels/$folder/" . date("Ym", time()) . '/' . date("d", time()) . '/';

        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值如：/home/vagrant/Code/yusscg/public/uploads/images/init/201709/21/
        $upload_path = public_path() . '/' . $folder_name;

        // 获取文件的后缀名，因文件从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'xls';

        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1_1493521050_7BVc9v9ujP.xls
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        // 如果上传的不是Excel将终止操作
        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        // 将文件移动到我们的目标存储路径中
        $file->move($upload_path, $filename);

        return [
            'path' => "$upload_path$filename"
        ];
    }
}