<?php

namespace App\Handlers;

use Image;

class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowed_ext = ['png', 'jpeg', 'jpg', 'jif'];

    public function save($file, $folder, $file_prefix, $max_width = false)
    {
        // 构建文件加命名规则，例如 uploads/images/avatars/201902/25
        $folder_name = 'uploads/images/' . $folder . '/' . date('Ym/d', time());

        // 文件具体储存的屋里路径，public_path() 是「public」目录的屋里路径
        $upload_path = public_path() . '/' . $folder_name;

        // 获取文件的后缀名
        $extension = strtolower($file->getClientOriginalExtension()) ?? 'png';

        // 上传的不是图片终止上传
        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        // 拼接文件名
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        // 移动图片到目标储存器
        $file->move($upload_path, $filename);

        // 如果限制了图片宽度，就进行裁剪
        if ($max_width && $extension != 'gif') {
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name/$filename"
        ];
    }

    public function reduceSize($file_path, $max_width)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例双方缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}