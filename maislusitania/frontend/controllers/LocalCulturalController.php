<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use common\models\Favorito;
use common\models\LocalCultural;
use common\models\TipoLocal;
use common\models\Distrito;

use frontend\models\LocalCulturalSearch;

/**
 * LocalCulturalController implements the CRUD actions for LocalCultural model.
 */
class LocalCulturalController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index', 'view'],
                            'allow' => true,
                            'roles' => ['?', 'viewPlace'],
                        ],
                        [
                            'actions' => ['toggle-favorite'],
                            'allow' => true,
                            'roles' => ['removeFavorite'],
                        ],
                    ],
                
                ],
            ]
        );
    }

    /**
     * Lists all LocalCultural models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LocalCulturalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $dataProvider->pagination->pageSize = 10;

        // Buscar tipos de local para dropdown
        $tiposLocal = ArrayHelper::map(
            TipoLocal::find()->orderBy('nome')->all(),
            'id',
            'nome'
        );

        // Buscar distritos para dropdown
        $distritos = ArrayHelper::map(
            Distrito::find()->orderBy('nome')->all(),
            'id',
            'nome'
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tiposLocal' => $tiposLocal,
            'distritos' => $distritos,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $averageRating = $this->findModel($id)->getAverageRating();
        $ratingCount = $this->findModel($id)->getRatingCount();
        //$horario = $this->findModel($id)->horarios;
        //$eventos = $this->findModel($id)->getEventos()->all();
        //$noticias = $this->findModel($id)->noticias;
        $bilhetes = $this->findModel($id)->getTipoBilhetes()->all();

        return $this->render('view', [
            'model' => $model,
            'averageRating' => $averageRating,
            'ratingCount' => $ratingCount,
            //'horario' => $horario,
            //'eventos' => $eventos,
            //'noticias' => $noticias,
            'bilhetes' => $bilhetes,

        ]);
    }

    public function actionToggleFavorite($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $local = $this->findModel($id);
        $userId = Yii::$app->user->id;

        // Verificar se já existe usando query direta
        $favorito = Favorito::findOne([
            'utilizador_id' => $userId,
            'local_id' => $id,
        ]);
        
        if ($favorito !== null) {
            // Existe, então remover
            $favorito->delete();
        } else {
            // Se não existe, adicionar
            $favorito = new Favorito();
            $favorito->utilizador_id = $userId;
            $favorito->local_id = $id;
            $favorito->data_adicao = date('Y-m-d H:i:s');
            
            if (!$favorito->save()) {
                Yii::$app->session->setFlash('error', 'Erro ao adicionar aos favoritos.');
            }
        }

        if (Yii::$app->request->isPjax) {
            return $this->actionIndex();
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    protected function findModel($id)
    {
        if (($model = LocalCultural::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
