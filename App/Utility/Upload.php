<?php

namespace App\Utility;

class Upload
{
    public static function uploadFile($file, $fileName)
    {
        self::validateFile($file);

        $storageDirectory = dirname(__DIR__, 2) . '/public/storage/';
        if (!is_dir($storageDirectory)) {
            mkdir($storageDirectory, 0777, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $pictureName = basename($fileName . '.' . $extension);
        $uploadPath = $storageDirectory . $pictureName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new \Exception("An error occurred. Please contact the administrator.");
        }

        return $pictureName;
    }

    public static function validateFile($file)
    {
        if (!isset($file['name'], $file['size'])) {
            throw new \Exception('Aucun fichier image valide.');
        }

        $fileExtensionsAllowed = ['jpeg', 'jpg', 'png'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $fileExtensionsAllowed, true)) {
            throw new \Exception('This file extension is not allowed. Please upload a JPEG or PNG file');
        }

        if ($file['size'] > 4000000) {
            throw new \Exception('File exceeds maximum size (4MB)');
        }

        return true;
    }
}
