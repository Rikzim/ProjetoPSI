<?php

namespace backend\controllers;

use Yii;
use common\models\LocalCultural;
use common\models\Horario;
use common\models\Distrito;
use common\models\TipoLocal;
use backend\models\LocalCulturalSearch;
use common\models\UploadForm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

class LocalCulturalController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['accessBackoffice', 'viewPlace'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['accessBackoffice', 'addPlace'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['accessBackoffice', 'editPlace'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['accessBackoffice', 'deletePlace'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LocalCultural models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocalCulturalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $locais = LocalCultural::find()->all();
        $distritos = ArrayHelper::map(Distrito::find()->all(), 'id', 'nome');
        $tipoLocais = ArrayHelper::map(TipoLocal::find()->all(), 'id', 'nome');

        //Paginação
        $dataProvider->pagination->pageSize = 10;


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'locais' => $locais,
            'distritos' => $distritos,
            'tipoLocais' => $tipoLocais,

        ]);
    }

    /**
     * Displays a single LocalCultural model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new LocalCultural();
        $uploadForm = new UploadForm();
        $horario = new Horario();
        $tipoLocais = ArrayHelper::map(TipoLocal::find()->all(), 'id', 'nome');
        $distritos = ArrayHelper::map(Distrito::find()->all(), 'id', 'nome');

        if (Yii::$app->request->isPost) {
            if (
                $model->load(Yii::$app->request->post()) &&
                $horario->load(Yii::$app->request->post())
            ) {
                $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
                if ($uploadForm->imageFile) {
                    $fileName = uniqid('local_') . '.' . $uploadForm->imageFile->extension;
                    if ($uploadForm->upload($fileName)) {
                        $model->imagem_principal = $fileName;
                    }
                }
                
                // Save horario first to get an ID, then link it to the model and save the model.
                if ($horario->save()) {
                    $model->horario_id = $horario->id;
                    if ($model->save()) {
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'uploadForm' => $uploadForm,
            'horario' => $horario,
            'distritos' => $distritos,
            'tipoLocais' => $tipoLocais,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $tipoLocais = ArrayHelper::map(TipoLocal::find()->all(), 'id', 'nome');
        $distritos = ArrayHelper::map(Distrito::find()->all(), 'id', 'nome');
        $uploadForm = new UploadForm();
        $horario = $model->horario ?: new Horario();


        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                
                $horario->load(Yii::$app->request->post());

                $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
                if ($uploadForm->imageFile) {
                    $fileName = uniqid('local_') . '.' . $uploadForm->imageFile->extension;

                    if ($uploadForm->upload($fileName)) {
                        // Remover imagem antiga se existir
                        $currentImage = $model->imagem_principal;
                        if (!empty($currentImage)) {
                            $oldImagePath = Yii::getAlias('@uploadPath') . '/' . $currentImage;
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }

                        $model->imagem_principal = $fileName;
                    }
                }

                if ($model->save()) {
                    if ($horario->save()) {
                        if (!$model->horario_id) {
                            $model->horario_id = $horario->id;
                            $model->save();
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Local Cultural atualizado!');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'uploadForm' => $uploadForm,
            'horario' => $horario,
            'tipoLocais' => $tipoLocais,
            'distritos' => $distritos,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Local Cultural deletado com sucesso!');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao deletar o Local Cultural.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = LocalCultural::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
