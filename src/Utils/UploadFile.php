<?php

namespace App\Utils;

class UploadFile {
    public static function uploadFile($file, $targetDirectory, $customFileName = null) {
        $fileName = $customFileName ?? basename($file["name"]);
        $targetPath = "uploads/$targetDirectory/$fileName";
        
        if(!file_exists("uploads/$targetDirectory")) {
            mkdir("uploads/$targetDirectory", 0777, true);
        }

        if(!isset($file["tmp_name"])) {
            if(copy($file, $targetPath)){
                return $fileName;
            } else {
                return false;
            }
        }

        if(move_uploaded_file($file["tmp_name"], $targetPath)){
            return $fileName;
        } else {
            return false;
        }
    }

    public static function removeFile($fileName, $targetDirectory) {
        $fullPath = "uploads/$targetDirectory/$fileName";

        if(file_exists($fullPath)) {
            unlink($fullPath);
            return true;
        } else {
            return false;
        }
    }
}