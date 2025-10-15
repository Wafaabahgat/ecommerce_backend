<?php

namespace App\Traits;

trait UploadImageTrait
{
    public function uploadImg($request, $folder, $fileName = 'image')
    {
        $path = null;
        if (!$request->hasFile($fileName)) {
            return null;
        }
        $file = $request->file($fileName);
        $filename = date('YmdHi') . $file->getClientOriginalName();
        $path = $file->store($folder, [
            'disk' => 'public'
        ]);
        return $path;
    }
}
