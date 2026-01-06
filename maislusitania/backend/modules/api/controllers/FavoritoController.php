<?php
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
use common\models\Favorito;
use common\models\LocalCultural;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\filters\AccessControl;

class FavoritoController extends ActiveController
{
    // ========================================
    // Define o modelo
    // ========================================
    public $modelClass = 'common\models\Favorito';
    
    // ========================================
    // Configura data provider
    // ========================================
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['index']);
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
        // Resposta em JSON
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
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
                    'roles' => ['viewFavorites'],
                ],
                [
                    'actions' => ['add'],
                    'allow' => true,
                    'roles' => ['addFavorite'],
                ],
                [
                    'actions' => ['remove'],
                    'allow' => true,
                    'roles' => ['removeFavorite'],
                ],
                [
                    'actions' => ['toggle'],
                    'allow' => true,
                    'roles' => ['addFavorite', 'removeFavorite'],
                ],
            ],
        ];

        return $behaviors;
    }

    // Lista todos os favoritos do utilizador autenticado
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        $modelClass = $this->modelClass;
        $favoritos = $modelClass::find()
            ->orderBy(['id' => SORT_DESC])
            ->where(['utilizador_id' => $user->id])
            ->all();

        if (empty($favoritos)) {
            Yii::$app->response->statusCode = 200;
            return [];
        }
        
        $data = array_map(function($favorito) {
            return [
                'id' => $favorito->id,
                'utilizador_id' => $favorito->utilizador_id,
                'local_id' => $favorito->local_id,
                'local_imagem' => $favorito->local->getImageAPI(),
                'local_nome' => $favorito->local->nome,
                'local_distrito' => $favorito->local->distrito->nome,
                'local_rating' => $favorito->local->getAverageRating(),
                'data_adicao' => $favorito->data_adicao,
                'isFavorite' => true,
            ];
        }, $favoritos);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }
    // ========================================
    // Extra Patterns
    // ========================================

    // Adiciona um favorito
    public function actionAdd($localid)
    {
        $user = Yii::$app->user->identity;

        $favoritoExistente = Favorito::getFavoriteByUserAndLocal($user->id, $localid);

        if ($favoritoExistente) {
            return ['status' => 'error', 'message' => 'Favorito já existe'];
        }

        $novoFavorito = new Favorito();
        $novoFavorito->utilizador_id = $user->id;
        $novoFavorito->local_id = $localid;

        if ($novoFavorito->save()) {
            return ['status' => 'success', 'message' => 'Favorito adicionado com sucesso'];
        } else {
            return ['status' => 'error', 'errors' => $novoFavorito->errors];
        }
    }

    // Remove um favorito
    public function actionRemove($localid)
    {
        $user = Yii::$app->user->identity;

        $favorito = Favorito::getFavoriteByUserAndLocal($user->id, $localid);

        if (!$favorito) {
            return ['status' => 'error', 'message' => 'Favorito não encontrado'];
        }

        if ($favorito->delete()) {
            return ['status' => 'success', 'message' => 'Favorito removido com sucesso'];
        } else {
            return ['status' => 'error', 'message' => 'Erro ao remover favorito'];
        }
    }

    // Alterna o estado de favorito
    public function actionToggle($localid)
    {
        $user = Yii::$app->user->identity;

        $favorito = Favorito::getFavoriteByUserAndLocal($user->id, $localid);

        if ($favorito) {
            // Se já existe, remove o favorito
            $favorito->delete();
            return ['status' => 'removed'];
        } else {
            // Se não existe, cria um novo favorito
            $novoFavorito = new Favorito();
            $novoFavorito->utilizador_id = $user->id;
            $novoFavorito->local_id = $localid;
            if ($novoFavorito->save()) {
                return ['status' => 'added'];
            } else {
                return ['status' => 'error', 'errors' => $novoFavorito->errors];
            }
        }
    }
}