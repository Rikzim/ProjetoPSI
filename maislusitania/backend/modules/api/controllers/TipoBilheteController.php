<?php
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\Cors;

class TipoBilheteController extends ActiveController
{
    // ========================================
    // Define o modelo
    // ========================================
    public $modelClass = 'common\models\TipoBilhete';

    // ========================================
    // Configura data provider
    // ========================================
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['view']);
        return $actions;
    }

    public function prepareDataProvider()
    {
        $modelClass = $this->modelClass;
        
        $query = $modelClass::find()->orderBy(['id' => SORT_DESC]);

        // Filter by local_id if provided in the URL
        $local_id = \Yii::$app->request->get('local_id');
        if ($local_id) {
            $query->andWhere(['local_id' => $local_id]);
        }

        return new ActiveDataProvider([
            'query' => $query, 
            'pagination' => [
                'pageSize' => 20, 
            ],
        ]);
    }

    public function actionView($id)
    {
        $modelClass = $this->modelClass;
        
        $bilhetes = $modelClass::find()
            ->where(['local_id' => $id, 'ativo' => 1])
            ->orderBy(['preco' => SORT_ASC])
            ->all();

        if (empty($bilhetes)) {
            \Yii::$app->response->statusCode = 404;
            return ['error' => 'Nenhum tipo de bilhete encontrado para este local.'];
        }

        return $bilhetes;
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
        
        return $behaviors;
    } 
}