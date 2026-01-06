<?php

namespace backend\modules\api\controllers;

use yii\rest\Controller;

class DefaultController extends Controller
{
    /**
     * GET /api
     * Retorna informações sobre a API
     */
    public function actionIndex()
    {
        return [
            'name' => 'MaisLusitania API',
            'version' => '1.0.0',
            'status' => 'online',
            'endpoints' => [
                '/api/avaliacaos' => ' /avaliacaos - Gestão de avaliações',
                '/api/eventos' => ' /eventos - Gestão de eventos',
                '/api/noticias' => ' /noticias - Gestão de notícias',
                '/api/favoritos' => ' /favoritos - Gestão de favoritos',
                '/api/local-culturals' => ' /local-culturals - Gestão de locais culturais',
                '/api/tipo-bilhetes' => ' /tipo-bilhetes - Gestão de tipos de bilhete',
                '/api/tipo-locals' => ' /tipo-locals - Gestão de tipos de local',
                '/api/user-profile' => ' /user-profile - Gestão de perfis de utilizador',
                '/api/mapa' => ' /mapa - Gestão de mapa',
                '/api/reservas' => ' /reservas - Gestão de reservas',
                '...'
                //RESTANTAS ENDPOINTS AQUI
            ],
        ];
    }

}