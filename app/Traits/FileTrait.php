<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait FileTrait
{
    /**
     * updload file
     *
     * @param  UploadFile  $file
     * @param  string  $path
     * @return string
     */
    public function upload(UploadedFile $file, $path)
    {
        $fileName = time().'.'.$file->getClientoriginalExtension();
        $result = $file->storeAs($path, $fileName, 'public');

        return $result;
    }
}
