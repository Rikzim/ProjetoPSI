<?php
namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
use yii\filters\AccessControl;

class AvaliacaoController extends ActiveController
{
    // ========================================
    // Define o modelo
    // ========================================
    public $modelClass = 'common\models\Avaliacao';

    // ========================================
    // Configura data provider
    // ========================================
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $modelClass = $this->modelClass;
        
        return new ActiveDataProvider([
            'query' => $modelClass::find()->orderBy(['id' => SORT_DESC]), 
            'pagination' => [
                'pageSize' => 20, 
            ],
        ]);
    }

    // ========================================
    // Controle de permissões
    // ========================================
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if (!is_array($behaviors)) {
            $behaviors = [];
        }

        // CORS para todos os controllers
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET','POST','PUT','DELETE','OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];
        // Autenticação via token
        $behaviors['authenticator'] = [ 
            'class' => QueryParamAuth::class,
        ];
        // Controle de acesso
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'view'],
                    'allow' => true,
                    'roles' => ['@'], // Apenas utilizadores autenticados
                ],
                [
                    'actions' => ['add'],
                    'allow' => true,
                    'roles' => ['addReview'],
                ],
                [
                    'actions' => ['remove'],
                    'allow' => true,
                    'roles' => ['deleteOwnReview', 'deleteAnyReview'],
                ],
            ],
        ];

        return $behaviors;
    } 

    // ========================================
    // Extra Patterns
    // ========================================

    // Adiciona uma nova avaliação
    public function actionAdd($localid)
    {
        $user = Yii::$app->user->identity;

        $model = new $this->modelClass;

        $model->local_id = $localid;
        $model->utilizador_id = $user->id;
        $model->classificacao = Yii::$app->request->post('classificacao');
        $model->comentario = Yii::$app->request->post('comentario', null);
        $model->data_avaliacao = date('Y-m-d H:i:s');
        $model->ativo = 1;

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return $model;
        } else {
            Yii::$app->response->statusCode = 400;
            return ['errors' => $model->errors];
        }
    }

    // Remove uma avaliação
    public function actionRemove($id){
        $user = Yii::$app->user;
        
        $modelClass = $this->modelClass;

        $avaliacao = null;
        if ($user->can('deleteAnyReview')) {
            $avaliacao = $modelClass::findOne($id);
        } else {
            $avaliacao = $modelClass::findOne(['id' => $id, 'utilizador_id' => $user->id]);
        }

        if (!$avaliacao) {
            Yii::$app->response->statusCode = 404;
            return ['status' => 'error', 'message' => 'Avaliação não encontrada'];
        }

        if ($avaliacao->delete()) {
            Yii::$app->response->statusCode = 200;
            return ['status' => 'success', 'message' => 'Avaliação removida com sucesso'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => 'error', 'message' => 'Erro ao remover avaliação'];
        }
    }
}