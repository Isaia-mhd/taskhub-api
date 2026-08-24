<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileStorageInterface
{
    public function upload(UploadedFile $file, string $directory): string;
    public function delete(string $path): bool;

    public function exists(string $path): bool;

    public function url(string $path): string;
}
