<?php
namespace backend\modules\api\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;
use common\models\Distrito;
use common\models\LocalCultural;
use common\models\TipoLocal;
use yii\filters\ContentNegotiator;
use yii\filters\auth\QueryParamAuth;
use Yii;
use yii\filters\AccessControl;

class LocalCulturalController extends ActiveController
{
    // ========================================
    // Define o modelo
    // ========================================
    public $modelClass = 'common\models\LocalCultural';
    // ========================================
    // Configura data provider
    // ========================================
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']); // Remover ação view padrão para personalização
        unset($actions['index']); // Remover ação index padrão para personalização
        unset($actions['create']); // Remover ação create padrão que nao é usada
        unset($actions['update']); // Remover ação update padrão que nao é usada
        unset($actions['delete']); // Remover ação delete padrão que nao é usada
        return $actions;
    }
    // ========================================
    // Define campos a retornar
    // ========================================
    public function fields()
    {
        return [
            'id',
            'nome',
            'tipoLocal',
            'distrito',
            'imagem',
            'morada',
            'descricao',
            'contacto_telefone',
            'contacto_email',
            'website',
            'ativo',
            'latitude',
            'longitude',
        ];
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

        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'optional' => ['index', 'view', 'distrito', 'tipo-local', 'search'], // Ações que não requerem autenticação
        ];

        $behaviors['contentNegotiator'] = [ // Resposta em JSON
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'view', 'distrito', 'tipo-local', 'search', 'avaliacoes'], // Ações com acesso público
                    'allow' => true,
                    'roles' => ['?', '@'],
                ],
            ],
        ];
        return $behaviors;
    }

    // Lista todos os locais culturais ativos
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $locais = $modelClass::find()
            ->where(['ativo' => true])
            ->with(['distrito', 'tipoLocal'])
            ->all();

        $userId = Yii::$app->user->id;

        if (empty($locais)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Nenhum local cultural encontrado.'];
        }

        $data = array_map(function($local) use ($userId) {
            $result = [
                'id' => $local->id,
                'nome' => $local->nome,
                'morada' => $local->morada,
                'distrito' => $local->distrito->nome ?? null,
                'descricao' => $local->descricao,
                'imagem' => $local->getImageAPI(),
                'avaliacao_media' => $local->getAverageRating(),
            ];

            if ($userId) {
                $result['favorito'] = $local->isFavoritedByUser($userId);
                $result['favorito_id'] = $local->getFavoritoIdByUser($userId);
            }
            
            return $result;
        }, $locais);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // Visualiza um local específico por ID
    public function actionView($id)
    {
        $modelClass = $this->modelClass;
        $local = $modelClass::find()
            ->where(['id' => $id, 'ativo' => true])
            ->with([
                'avaliacaos' => function($query) {
                    $query->andWhere(['ativo' => true]);
                },
                'noticias',
                'eventos',
                'tipoBilhetes' => function($query) {
                    $query->andWhere(['ativo' => true]);
                },
                'horario',
                'tipoLocal',
                'distrito'
            ])
            ->one();

        if (!$local) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Local cultural não encontrado.'];
        }
        return $this->formatLocalData($local);
    }

    // ========================================
    // Extra Patterns
    // ========================================

    // Filtra locais culturais por distrito
    public function actionDistrito($nome)
    {
        $distrito = Distrito::find()
            ->where(['LIKE', 'LOWER(nome)', strtolower($nome)])
            ->one();

        if (!$distrito) {
            throw new NotFoundHttpException("Distrito '$nome' não encontrado.");
        }

        // Retorna os locais culturais desse distrito
        $locais = LocalCultural::find()
            ->where(['distrito_id' => $distrito->id, 'ativo' => true])
            ->with(['distrito', 'tipoLocal']) // Adicionado para otimizar
            ->all();

        if (empty($locais)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhum local cultural encontrado para o distrito '$nome'."];
        }

        $userId = Yii::$app->user->id;
        $data = array_map(function($local) use ($userId) {
            $result = [
                'id' => $local->id,
                'nome' => $local->nome,
                'morada' => $local->morada,
                'distrito' => $local->distrito->nome ?? null,
                'descricao' => $local->descricao,
                'imagem' => $local->getImageAPI(),
                'avaliacao_media' => $local->getAverageRating(),
            ];

            if ($userId) {
                $result['favorito'] = $local->isFavoritedByUser($userId);
                $result['favorito_id'] = $local->getFavoritoIdByUser($userId);
            }
            return $result;
        }, $locais);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));
        Yii::$app->response->headers->set('X-Nome-Distrito', $distrito->nome ?? '');

        return $data;
    }

    // Filtra locais culturais por tipo de local
    public function actionTipoLocal($nome)
    {
        $tipolocal = TipoLocal::find()
            ->where(['LIKE', 'LOWER(nome)', strtolower($nome)])
            ->one();

        if (!$tipolocal) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Tipo de local '$nome' não encontrado."];
        }

        // Retorna os locais culturais desse tipo
        $locais = LocalCultural::find()
            ->where(['tipo_id' => $tipolocal->id, 'ativo' => true])
            ->with(['distrito', 'tipoLocal']) // Adicionado para otimizar
            ->all();

        if (empty($locais)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhum local cultural encontrado para o tipo '$nome'."];
        }

        $userId = Yii::$app->user->id;
        $data = array_map(function($local) use ($userId) {
            $result = [
                'id' => $local->id,
                'nome' => $local->nome,
                'morada' => $local->morada,
                'distrito' => $local->distrito->nome ?? null,
                'descricao' => $local->descricao,
                'imagem' => $local->getImageAPI(),
                'avaliacao_media' => $local->getAverageRating(),
            ];

            if ($userId) {
                $result['favorito'] = $local->isFavoritedByUser($userId);
                $result['favorito_id'] = $local->getFavoritoIdByUser($userId);
            }
            return $result;
        }, $locais);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // Pesquisa locais culturais por nome (suporta múltiplas palavras)
    public function actionSearch($nome)
    {
        $query = LocalCultural::find()
            ->where(['ativo' => true]);

        // Divide em palavras e procura por cada uma
        $palavras = explode(' ', trim($nome));
        
        foreach ($palavras as $palavra) {
            if (!empty($palavra)) {
                $query->andWhere(['LIKE', 'LOWER(nome)', strtolower($palavra)]);
            }
        }

        $locais = $query->with('distrito')->all();

        if (empty($locais)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => "Nenhum local cultural encontrado com o nome '$nome'."];
        }

        $userId = Yii::$app->user->id;

        $data = array_map(function($local) use ($userId) {
            $result = [
                'id' => $local->id,
                'nome' => $local->nome,
                'morada' => $local->morada,
                'distrito' => $local->distrito->nome ?? null,
                'descricao' => $local->descricao,
                'imagem' => $local->getImageAPI(),
                'avaliacao_media' => $local->getAverageRating(),
            ];

            if ($userId) {
                $result['favorito'] = $local->isFavoritedByUser($userId);
                $result['favorito_id'] = $local->getFavoritoIdByUser($userId);
            }

            return $result;
        }, $locais);
        
        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // Obtém avaliações de um local cultural específico
    public function actionAvaliacoes($id)
    {
        $modelClass = $this->modelClass;
        $local = $modelClass::find()
            ->where(['id' => $id, 'ativo' => true])
            ->with(['avaliacaos' => function($query) {
                $query->andWhere(['ativo' => true]);
            }])
            ->one();

        if (!$local) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Local cultural não encontrado.'];
        }

        $avaliacoes = $local->avaliacaos;

        if (empty($avaliacoes)) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Nenhuma avaliação encontrada para este local cultural.'];
        }

        $data = array_map(function($avaliacao) {
            return [
                'id' => $avaliacao->id,
                'utilizador' => $avaliacao->user->username ?? 'Anônimo',
                'classificacao' => (float)$avaliacao->classificacao,
                'comentario' => $avaliacao->comentario,
                'data_avaliacao' => date('Y-m-d', strtotime($avaliacao->data_avaliacao)),
                'ativo' => (bool)$avaliacao->ativo,
            ];
        }, $avaliacoes);

        Yii::$app->response->headers->set('X-Total-Count', (string)count($data));

        return $data;
    }

    // Método auxiliar para formatar os dados do local
    private function formatLocalData($local)
    {
        $userId = Yii::$app->user->id;

        $data = [
            'id' => $local->id,
            'nome' => $local->nome,
            'tipo' => $local->tipoLocal->nome ?? null,
            'distrito' => $local->distrito->nome ?? null,
            'imagem' => $local->getImageAPI(),
            'morada' => $local->morada,
            'descricao' => $local->descricao,
            'contacto_telefone' => $local->contacto_telefone,
            'contacto_email' => $local->contacto_email,
            'website' => $local->website,
            'ativo' => (bool)$local->ativo,
            'latitude' => (float)$local->latitude,
            'longitude' => (float)$local->longitude,
            'horario' =>  $local->horario ? [
                'segunda' => $local->horario->segunda ?? null,
                'terca' => $local->horario->terca ?? null,
                'quarta' => $local->horario->quarta ?? null,
                'quinta' => $local->horario->quinta ?? null,
                'sexta' => $local->horario->sexta ?? null,
                'sabado' => $local->horario->sabado ?? null,
                'domingo' => $local->horario->domingo ?? null,
            ] : "Horário não disponível",
            'avaliacoes' => $local->avaliacaos ? array_map(function($avaliacao) {
                return [
                    'id' => $avaliacao->id,
                    'local_id' => $avaliacao->local_id,
                    'utilizador_id' => $avaliacao->utilizador_id,
                    'utilizador' => $avaliacao->utilizador->username ?? 'Anónimo',
                    'classificacao' => (float)$avaliacao->classificacao,
                    'comentario' => $avaliacao->comentario,
                    'data_avaliacao' => date('Y-m-d', strtotime($avaliacao->data_avaliacao)),
                    'ativo' => (bool)$avaliacao->ativo,
                ];
            }, $local->avaliacaos) : "Ainda não existem avaliações. Seja o primeiro a avaliar!",
            'noticias' => $local->noticias ? array_map(function($noticia) {
                return [
                    'id' => $noticia->id,
                    'titulo' => $noticia->titulo,
                    'descricao' => $noticia->resumo,
                    'data_publicacao' => date('Y-m-d H:i', strtotime($noticia->data_publicacao)),
                    'imagem' => $noticia->getImageAPI(),
                ];
            }, $local->noticias) : "Nenhuma notícia relacionada disponível no momento. ",
            'eventos' => $local->eventos ? array_map(function($evento) {
                return [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'descricao' => $evento->descricao,
                    'data_inicio' => date('Y-m-d\TH:i:s', strtotime($evento->data_inicio)),
                    'data_fim' => date('Y-m-d\TH:i:s', strtotime($evento->data_fim)),
                    'imagem' => $evento->getImageAPI(),
                ];
            }, $local->eventos) : "Nenhum evento relacionado disponível no momento.",
            'tipos-bilhete' => $local->tipoBilhetes ? array_map(function($tipo) {
                return [
                    'id' => $tipo->id,
                    'nome' => $tipo->nome,
                    'descricao' => $tipo->descricao,
                    'preco' => number_format($tipo->preco, 2) . '€',
                    'ativo' => (bool)$tipo->ativo,
                ];
            }, $local->tipoBilhetes) : "Nenhum bilhete disponível no momento.",
        ];

        if ($userId) {
            $data['favorito'] = $local->isFavoritedByUser($userId);
            $data['favorito_id'] = $local->getFavoritoIdByUser($userId);
        }

        return $data;
    }
}