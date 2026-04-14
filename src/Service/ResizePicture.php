<?php

namespace App\Service;

class ResizePicture
{

    public function resize($img, $intX = 400, $intY = 400, $keepRatio = false)
    {
        $filename = $img;

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
            imagejpeg($image_p, $img, 90);
        } elseif ($extension == 'png') {
            imagepng($image_p, $img, 8);
        } elseif ($extension == 'webp') {
            imagewebp($image_p, $img, 85);
        }
    }
}
