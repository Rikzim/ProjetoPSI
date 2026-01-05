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
        
        $behaviors['authenticator'] = [
           
            'class' => QueryParamAuth::class,
            //only=> ['index'],  //Apenas para o GET
            
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'view'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => ['add'],
                    'allow' => true,
                    'roles' => ['addReview'],
                ],
                [
                    'actions' => ['edit'],
                    'allow' => true,
                    'roles' => ['editOwnReview', 'editAnyReview'],
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

    public function actionAdd($localid)
    {
        $modelClass = $this->modelClass;
        $userId = Yii::$app->user->id;

        // Procure uma avaliação existente deste utilizador para este local, ativa ou inativa.
        $model = $modelClass::findOne([
            'local_id' => $localid,
            'utilizador_id' => $userId,
        ]);

        // Se não existir nenhuma, crie uma nova.
        if ($model === null) {
            $model = new $this->modelClass;
            $model->local_id = $localid;
            $model->utilizador_id = $userId;
        }
        
        // Carregue os novos dados, atualize a data e defina como ativo.
        $model->load(Yii::$app->request->getBodyParams(), '');
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

    public function actionEdit($id)
    {
        $user = Yii::$app->user;
        $modelClass = $this->modelClass;

        $avaliacao = null;
        if ($user->can('editAnyReview')) {
            $avaliacao = $modelClass::findOne(['id' => $id, 'ativo' => 1]);
        } else {
            $avaliacao = $modelClass::findOne(['id' => $id, 'utilizador_id' => $user->id, 'ativo' => 1]);
        }

        if (!$avaliacao) {
            Yii::$app->response->statusCode = 404;
            return ['status' => 'error', 'message' => 'Avaliação não encontrada'];
        }

        $avaliacao->load(Yii::$app->request->getBodyParams(), '');

        if ($avaliacao->save()) {
            Yii::$app->response->statusCode = 200;
            return $avaliacao;
        } else {
            Yii::$app->response->statusCode = 400;
            return ['errors' => $avaliacao->errors];
        }
    }

    public function actionRemove($id){
        $user = Yii::$app->user;
        
        $modelClass = $this->modelClass;

        $avaliacao = null;
        if ($user->can('deleteAnyReview')) {
            $avaliacao = $modelClass::findOne(['id' => $id, 'ativo' => 1]);
        } else {
            $avaliacao = $modelClass::findOne(['id' => $id, 'utilizador_id' => $user->id, 'ativo' => 1]);
        }

        if (!$avaliacao) {
            Yii::$app->response->statusCode = 404;
            return ['status' => 'error', 'message' => 'Avaliação não encontrada'];
        }

        $avaliacao->ativo = 0;
        if ($avaliacao->save()) {
            Yii::$app->response->statusCode = 200;
            return ['status' => 'success', 'message' => 'Avaliação removida com sucesso'];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['status' => 'error', 'message' => 'Erro ao remover avaliação'];
        }
    }
}