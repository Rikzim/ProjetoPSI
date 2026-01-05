<?php

namespace backend\controllers;

use Yii;
use common\models\TipoLocal;
use common\models\UploadForm;
use backend\models\TipoLocalSearch;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * TipoLocalController implements the CRUD actions for TipoLocal model.
 */
class TipoLocalController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['accessBackoffice','viewTypePlace'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['accessBackoffice','addTypePlace'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['accessBackoffice','editTypePlace'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['accessBackoffice','deleteTypePlace'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TipoLocal models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TipoLocalSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination->pageSize = 10;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single TipoLocal model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TipoLocal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TipoLocal();
        $uploadForm = new UploadForm();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
                if ($uploadForm->imageFile) {
                    $fileName = uniqid('marker_') . '.' . $uploadForm->imageFile->extension;
                    if ($uploadForm->upload($fileName)) {
                        $model->icone = $fileName;
                    }
                }

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Tipo de Local criado com sucesso!');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'uploadForm' => $uploadForm,
        ]);
    }

    /**
     * Updates an existing TipoLocal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $uploadForm = new UploadForm();

        if ($this->request->isPost && $model->load($this->request->post())) {
            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
            if ($uploadForm->imageFile) {
                $fileName = uniqid('marker_') . '.' . $uploadForm->imageFile->extension;
                if ($uploadForm->upload($fileName)) {

                    // Remover imagem antiga se existir
                    $currentImage = $model->icone;
                    if (!empty($currentImage)) {
                        $oldImagePath = Yii::getAlias('@uploadPath') . '/' . $currentImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    $model->icone = $fileName;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Tipo de Local atualizado com sucesso!');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'uploadForm' => $uploadForm,
        ]);
    }

    /**
     * Deletes an existing TipoLocal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Tipo de Local deletado com sucesso!');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao deletar o Tipo de Local.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TipoLocal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TipoLocal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TipoLocal::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
