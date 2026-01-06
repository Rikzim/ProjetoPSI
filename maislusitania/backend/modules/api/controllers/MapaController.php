<?php

namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use Yii;

class MapaController extends ActiveController
{
    // ========================================
    // Define o modelo
    // ========================================
    public $modelClass = 'common\models\LocalCultural';

    // ========================================
    // Configura actions
    // ========================================
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']); // Remover ação view padrão
        unset($actions['index']); // Remover ação index padrão
        return $actions;
    }
    // ========================================
    // Controle de permissões
    // ========================================
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // CORS para todos os controllers
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET','POST','PUT','DELETE','OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];
        // Formato de resposta JSON
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        // Controle de acesso
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'search'],
                    'allow' => true,
                    'roles' => ['?', '@'], // Apenas utilizadores autenticados e convidados
                ],
            ],
        ];

        return $behaviors;
    }

    // Lista todos os locais e o seus tipos com a imagem
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $locais = $modelClass::find()
            ->where(['ativo' => true])
            ->all();

        if (!$locais) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhum local cultural encontrado."];
        }

        $data = array_map(function($local) {
            return [
                'id' => $local->id,
                'nome' => $local->nome,
                'imagem' => $local->getImageAPI(),
                'tipo' => $local->tipoLocal->nome,
                'latitude' => $local->latitude,
                'longitude' => $local->longitude,
                'markerImagem' => $local->tipoLocal->getImageAPI(),
            ];
        }, $locais);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // ========================================
    // Extra Patterns
    // ========================================

    // Pesquisa locais culturais por nome ou tipo
    public function actionSearch($nome)
    {
        $modelClass = $this->modelClass;
        $locais = $modelClass::find()
            ->joinWith('tipoLocal')
            ->where(['local_cultural.ativo' => true])
            ->andWhere(['or',
                ['like', 'LOWER(local_cultural.nome)', strtolower($nome)],
                ['like', 'LOWER(tipo_local.nome)', strtolower($nome)]
            ])
            ->all();

        if(!$locais) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhum local cultural encontrado com o nome '$nome'."];
        }

        $data = array_map(function($local) {
            return [
                'id' => $local->id,
                'nome' => $local->nome,
                'imagem' => $local->getImageAPI(),
                'tipo' => $local->tipoLocal->nome,
                'latitude' => $local->latitude,
                'longitude' => $local->longitude,
                'markerImagem' => $local->tipoLocal->getImageAPI(),
            ];
        }, $locais);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }
}