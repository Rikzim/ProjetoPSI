<?php
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\Cors;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\auth\QueryParamAuth;
use Yii;
use yii\filters\AccessControl;

class NoticiaController extends ActiveController
{
    // ========================================
    // Define o modelo
    // ========================================
    public $modelClass = 'common\models\Noticia';

    // ========================================
    // Configura data provider
    // ========================================
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']); // Remover ação view padrão para personalização
        unset($actions['index']); // Remover ação index padrão para personalização
        unset($actions['create']); // Remover ação create padrão que não será usada
        unset($actions['update']); // Remover ação update padrão que não será usada
        unset($actions['delete']); // Remover ação delete padrão que não será usada
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
                    'actions' => ['index', 'view', 'tipo-local', 'search', 'data'],
                    'allow' => true,
                    'roles' => ['@'], // Apenas utilizadores autenticados
                ],
            ],
        ];
        return $behaviors;
    } 

    // Lista todas as notícias ativas
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $noticias = $modelClass::find()
            ->where(['ativo' => true])
            ->all();

        if (empty($noticias)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhuma notícia encontrada."];
        }

        $data = array_map(function($noticia) {
            return [
                'id' => $noticia->id,
                'nome' => $noticia->titulo,
                'local_nome' => $noticia->local->nome ?? null,
                'resumo' => $noticia->resumo,
                'imagem' => $noticia->getImageAPI(),
                'data_publicacao' => $noticia->data_publicacao,
            ];
        }, $noticias);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // Obtém detalhes de uma notícia específica
    public function actionView($id)
    {   
        $modelClass = $this->modelClass;
        $noticia = $modelClass::find()
            ->where(['id' => $id, 'ativo' => true])
            ->one();

        if (!$noticia) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Notícia não encontrada."];
        }

        $data = array_map(function($noticia) {
            return [
                'id' => $noticia->id,
                'nome' => $noticia->titulo,
                'conteudo' => $noticia->conteudo,
                'imagem' => $noticia->getImageAPI(),
                'data_publicacao' => $noticia->data_publicacao,
            ];
        }, [$noticia]);

        return $data;
    }
    // ========================================
    // Extra Patterns
    // ========================================

    // Filtra notícias por tipo de local
    public function actionTipoLocal($nome)
    {
        $modelClass = $this->modelClass;
        $noticias = $modelClass::find()
            ->joinWith('local.tipoLocal') // ALTERADO: tipoLocal para tipo
            ->where(['LIKE', 'LOWER(tipo_local.nome)', strtolower($nome)])
            ->andWhere(['noticia.ativo' => true])
            ->all();
            
        if (empty($noticias)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Nenhuma noticia encontrada com esse tipo de local.'];
        }

        $data = array_map(function($noticia) {
            return [
                'id' => $noticia->id,
                'titulo' => $noticia->titulo,
                'resumo' => $noticia->resumo,
                'imagem' => $noticia->getImageAPI(),
                'data_publicacao' => $noticia->data_publicacao,
            ];
        }, $noticias);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // Filtra notícias por data de publicação
    public function actionData($data)
    {
        $modelClass = $this->modelClass;
        $noticias = $modelClass::find()
            ->where(['DATE(data_publicacao)' => $data, 'ativo' => true])
            ->all();

        if (empty($noticias)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhuma notícia encontrada para a data '$data'."];
        }

        $data = array_map(function($noticia) {
            return [
                'id' => $noticia->id,
                'nome' => $noticia->titulo,
                'local_nome' => $noticia->local->nome ?? null,
                'resumo' => $noticia->resumo,
                'imagem' => $noticia->getImageAPI(),
                'data_publicacao' => $noticia->data_publicacao,
            ];
        }, $noticias);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // Pesquisa notícias por nome (suporta múltiplas palavras)
    public function actionSearch($nome)
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find()
            ->where(['LIKE', 'LOWER(titulo)', strtolower($nome)])
            ->andWhere(['ativo' => true]);

        // Divide em palavras e procura por cada uma
        $palavras = explode(' ', trim($nome));
        
        foreach ($palavras as $palavra) {
            if (!empty($palavra)) {
                $query->andWhere(['LIKE', 'LOWER(titulo)', strtolower($palavra)]);
            }
        }

        // Executa a consulta
        $noticias = $query->all();

        if (empty($noticias)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhuma notícia encontrada com o nome '$nome'."];
        }

        $userId = Yii::$app->user->id;

        $data = array_map(function($noticia) {
            return [
                'id' => $noticia->id,
                'nome' => $noticia->titulo,
                'local_nome' => $noticia->local->nome ?? null,
                'resumo' => $noticia->resumo,
                'imagem' => $noticia->getImageAPI(),
                'data_publicacao' => $noticia->data_publicacao,
            ];
        }, $noticias);
        
        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }
}