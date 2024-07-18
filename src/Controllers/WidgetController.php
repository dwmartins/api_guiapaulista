<?php

namespace App\Controllers;

use App\Class\Widget;
use App\Http\Request;
use App\Http\Response;
use Exception;

class WidgetController {
    public function floatingButton(Request $request, Response $response) {
        try {
            $requestData = $request->body();

            if(!empty($requestData['widget_data']['phone'])) {
                if(!ctype_digit($requestData['widget_data']['phone'])) {
                    return $response->json([
                        'error' => true,
                        'message' => "O campo (Telefone) contem caracteres inválidos."
                    ]);
                }
            }

            $widget = new Widget();
            $widget->getWidgetById($requestData['id']);
            $data = $widget->getData();

            $fieldsToUpdate = ['active', 'useBasicInformationPhone', 'position', 'phone'];

            foreach ($fieldsToUpdate as $field) {
                if (isset($requestData['widget_data'][$field])) {
                    $data[$field] = $requestData['widget_data'][$field];
                }
            }

            $widget->setData($data);
            $widget->save();

            $response->json([
                'success' => true,
                'message' => "Widget Atualizado com sucesso.",
                'siteInfoData' => $widget->toArray()
            ], 201);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao salvar as informações do widget."
            ], 500);
        }
    }

    public function fetch(Request $request, Response $response) {
        try {
            $widget = new Widget();
            $widget->getWidgets();

            $response->json($widget->getWidgets());
        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao buscar as informações do widget."
            ], 500);
        }
    }

}
