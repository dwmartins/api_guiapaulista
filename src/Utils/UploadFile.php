<?php

namespace App\Utils;

use Exception;

class UploadFile {
    public static function uploadFile($file, $targetDirectory, $customFileName = null) {
        try {
            $fileName = $customFileName ?? basename($file["name"]);
            $targetPath = self::getTargetPath($targetDirectory, $fileName);

            self::createDirectoryIfNotExists($targetDirectory);
            self::moveFile($file, $targetPath);

        } catch (Exception $e) {
            logError($e->getMessage());
            throw $e;
        }
    }

    public static function removeFile($fileName, $targetDirectory) {
        $targetPath = self::getTargetPath($targetDirectory, $fileName);

        if(file_exists($targetPath)) {
            if (!unlink($targetPath)) {
                throw new \Exception("Failed to remove file: $targetPath");
            }
        }
    }

    private static function getTargetPath($targetDirectory, $fileName) {
        return __DIR__ . "/../../public/uploads/$targetDirectory/$fileName";
    }

    private static function createDirectoryIfNotExists($directory) {
        $fullPath = __DIR__ . "/../../public/uploads/$directory";
        
        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, 0777, true)) {
                throw new Exception("Failed to create directory: $fullPath");
            }
        }
    }

    private static function moveFile($file, $targetPath) {
        if (!isset($file["tmp_name"])) {
            if (!copy($file, $targetPath)) {
                throw new Exception("Failed to copy file to: $targetPath");
            }
            return true;
        }

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            throw new Exception("Failed to move file to: $targetPath");
        }

        return true;
    }
}