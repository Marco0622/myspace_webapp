<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    public function __construct(
        private string $sessionFileDirectory,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->guessExtension();

        $file->move($this->sessionFileDirectory, $filename);

        return $filename;
    }

    public function remove(string $filename): void
    {
        $filepath = $this->sessionFileDirectory . '/' . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}