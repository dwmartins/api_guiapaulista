<?php

namespace App\Validators;

use App\Class\SiteInfo;
use App\Http\Response;

class SiteInfoValidator {
    public static function create(SiteInfo $siteInfo) {
        $fields = [
            "Nome do site"    => $siteInfo->getWebSiteName(),
            "E-mail"          => $siteInfo->getEmail(),
            "Telefone"        => $siteInfo->getPhone(),
            "Horário"         => $siteInfo->getWorkSchedule(),
            "Cidade"          => $siteInfo->getCity(),
            "Estado"          => $siteInfo->getState(),
            "Endereço"        => $siteInfo->getAddress(),
            "Instagram"       => $siteInfo->getInstagram(),
            "Facebook"        => $siteInfo->getFacebook(),
            "Descrição"       => $siteInfo->getDescription(),
            "Palavras chaves" => $siteInfo->getKeywords(),
        ];

        foreach($fields as $key => $value) {
            if($key === "E-mail") {
                if(!TextValidator::email($value)) {
                    Response::json([
                        'error'     => true,
                        'message'   => "O e-mail é invalido"
                    ], 400);

                    return false;
                }

                continue;
            }

            if($key === "Instagram" || $key === "Facebook") {
                if(!TextValidator::isValidUrl($value)) {
                    Response::json([
                        'error'     => true,
                        'message'   => "O campo ($key) é invalido"
                    ], 400);

                    return false;
                }

                continue;
            }

            if(!TextValidator::fullText($value)) {
                Response::json([
                    'error'     => true,
                    'message'   => "O campo ($key) contem caracteres inválidos."
                ], 400);

                return false;
            }
        }

        return true;
    }
}