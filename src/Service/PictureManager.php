<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureManager
{   
    /**
     * Injection des différents repositories.
     */
     public function __construct(
        private string $userPhotosDirectory  
    ) {}

    /**
     * Redimension d'un image
     * 
     * @param string $img Nom de l'image.
     * @param int $intX largeur de l'image.
     * @param int $intY Hauter de l'image.
     * @param bool $keepRatio Permet de garde le ratio si true.
     */
    public function resize(string $img, int $intX = 400, int $intY = 400, bool $keepRatio = false)
    {
        $filename = $this->userPhotosDirectory . '/' .$img;

        list($width_orig, $height_orig) = getimagesize($filename);

        $width = $intX;
        $height = $intY;

        if ($keepRatio) {
            $ratio = $width_orig / $height_orig;

            $height = $intY;
            $width = $intY * $ratio;

            if ($width > $intX) {
                $width = $intX;
                $height = $intX / $ratio;
            }
        }

        $image_p = imagecreatetruecolor($width, $height);

        imagealphablending($image_p, false);
        imagesavealpha($image_p, true);

        $data = file_get_contents($filename);
        $image = imagecreatefromstring($data);

        imagecopyresampled(
            $image_p,
            $image,
            0,
            0,
            0,
            0,
            (int)$width,
            (int)$height,
            $width_orig,
            $height_orig
        );

        $extension = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        if ($extension == 'jpg' || $extension == 'jpeg') {
            imagejpeg($image_p, $filename, 90);
        } elseif ($extension == 'png') {
            imagepng($image_p, $filename, 8);
        } elseif ($extension == 'webp') {
            imagewebp($image_p, $filename, 85);
        }

        imagedestroy($image_p);
        imagedestroy($image);
    }

    /**
     * Téléchargement d'une image.
     * 
     * @param UploadedFile $file fichier à télécharger.
     * @param string $oldFilename Nom du fichier a supprimer.
     */
    public function upload(UploadedFile $file, ?string $oldFilename = null): string
    {

        $this->delete($oldFilename);

        $newFilename = uniqid() . '.' . $file->guessExtension();

        $file->move($this->userPhotosDirectory, $newFilename);

        return $newFilename;
    }

    /**
     * Suppression d'une image
     * 
     * @param string $filename nom de l'image a supprimer
     */
    public function delete(?string $filename): void
    {
        if (!$filename) return;

        $path = $this->userPhotosDirectory . '/' . $filename;

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
