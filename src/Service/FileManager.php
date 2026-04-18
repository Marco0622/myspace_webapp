<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Gestion des fichiers
 */
class FileManager
{
    /**
     * Injection des différents repositories.
     */
    public function __construct(
        private string $sessionFileDirectory,
    ) {}

    /**
     * Téléchargement d'un fichier.
     * 
     * @param UploadedFile $file fichier à télécharger
     */
    public function upload(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->guessExtension();

        $file->move($this->sessionFileDirectory, $filename);

        return $filename;
    }

    /**
     * Suppression d'un fichier.
     * 
     * @param string $filename nom du fichier a supprimer
     */
    public function remove(string $filename): void
    {
        $filepath = $this->sessionFileDirectory . '/' . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}