<?php

namespace App\Services\Storage;

use App\Interfaces\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3FileStorage implements FileStorageInterface
{

    public function upload(
        UploadedFile $file,
        string $directory
    ): string {
        return $file->store($directory, 's3');
    }

    public function delete(string $path): bool
    {
        return Storage::disk('s3')->delete($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('s3')->exists($path);
    }

    public function url(string $path): string
    {
        return Storage::disk('s3')->url($path);
    }
}
