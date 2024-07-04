<?php

namespace App\Controllers;

use App\Class\SiteInfo;
use App\Http\Request;
use App\Http\Response;
use App\Models\SiteInfoDAO;
use App\Validators\SiteInfoValidator;
use Exception;

class SiteInfoController {

    /**
     * Update website information.
     * MEthod HTTP: POST
     * 
     * Expected data in the request body should match the fields in the SiteInfo class
     * @return Response 
     */
    public function create(Request $request, Response $response) {
        try {
            $requestData = $request->body();

            if(!SiteInfoValidator::create(new SiteInfo($requestData))) {
                return;
            }

            $siteInfoExists = SiteInfoDAO::fetch();
            $siteInfo = new SiteInfo($requestData);

            if(empty($siteInfoExists)) {
                $siteInfo->save();
            } else {
                $siteInfo->update();
            }

            $response->json([
                'success' => true,
                'message' => "Informações do site salvas com sucesso."
            ], 201);
            
        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao salvar as informações do site."
            ], 500);
        }
    }

    public function fetch(Request $request, Response $response) {
        try {
            $siteInfo = new SiteInfo();
            
            return $response->json($siteInfo->fetch());

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao buscar as informações do site."
            ], 500);
        }
    }
}