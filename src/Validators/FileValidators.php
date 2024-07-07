<?php

namespace App\Validators;

class FileValidators {
    public static function validImage($file) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB em bytes

        if(!in_array($file['type'], $allowedTypes)) {
            return ["invalid" => "O arquivo deve ser uma imagem JPG, JPEG ou PNG."];
        }

        if($file['size'] > $maxFileSize) {
            return ["invalid" => "O arquivo não pode exceder 5MB."];
        }

        if($file['error'] !== UPLOAD_ERR_OK) {
            return ["invalid" => "Erro durante o upload do arquivo."];
        }

        $mimeType = explode("/" , $file["type"])[1];

        return [
            "fileName"  => $file["name"],
            "size"      => $file["size"],
            "mimeType"  => $mimeType
        ];
    }

    public static function validIcon($file) {
        $allowedTypes = ['image/vnd.microsoft.icon', 'image/x-icon', 'image/jpeg', 'image/jpg', 'image/png'];
        $maxFileSize = 5 * 1024 * 1024;
    
        if(!in_array($file['type'], $allowedTypes)) {
            return ["invalid" => "O arquivo deve ser um ícone ICO ou JPG, JPEG ou PNG."];
        }
    
        if($file['size'] > $maxFileSize) {
            return ["invalid" => "O arquivo não pode exceder 5MB."];
        }
    
        if($file['error'] !== UPLOAD_ERR_OK) {
            return ["invalid" => "Erro durante o upload do arquivo."];
        }
    
        $mimeType = explode("/" , $file["type"])[1];
    
        return [
            "fileName"  => $file["name"],
            "size"      => $file["size"],
            "mimeType"  => $mimeType
        ];
    }    
}