<?php

namespace App\Services\Storage;

use App\Interfaces\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalFileStorage implements FileStorageInterface
{
    public function upload( UploadedFile $file,string $directory ): string 
    {
        return $file->store($directory, 'local');
    }

    public function delete(string $path): bool
    {
        return Storage::disk('local')->delete($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('local')->exists($path);
    }

    public function url(string $path): string
    {
        return Storage::disk('local')->url($path);
    }
}
