<?php

namespace frontend\controllers;

use common\models\Reserva;
use common\models\LinhaReserva;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use Yii;

/**
 * ReservaController implements the CRUD actions for Reserva model.
 */
class ReservaController extends Controller
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
                        'actions' => ['index', 'view', 'download-ticket'],
                        'allow' => true,
                        'roles' => ['viewReservations'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['buyTickets'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Reserva models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $userId = Yii::$app->user->id;
        $hoje = date('Y-m-d');
        
        // Buscar reservas ativas e construir array de bilhetes individuais
        $reservasAtivas = Reserva::find()
            ->where(['utilizador_id' => $userId])
            ->andWhere(['>=', 'data_visita', $hoje])
            ->with(['local', 'linhaReservas', 'linhaReservas.tipoBilhete'])
            ->orderBy(['data_criacao' => SORT_DESC])
            ->all();
        
        $ticketsAtivos = [];
        foreach ($reservasAtivas as $reserva) {
            foreach ($reserva->linhaReservas as $linha) {
                for ($i = 1; $i <= $linha->quantidade; $i++) {
                    $ticketsAtivos[] = [
                        'reserva' => $reserva,
                        'linha' => $linha,
                        'ticketNumber' => $i,
                        'isExpirado' => false,
                    ];
                }
            }
        }
        
        $dataProviderAtivas = new ArrayDataProvider([
            'allModels' => $ticketsAtivos,
            'pagination' => [
                'pageSize' => 6,
                'pageParam' => 'page-ativos',
            ],
        ]);
        
        // Buscar reservas expiradas e construir array de bilhetes individuais
        $reservasExpiradas = Reserva::find()
            ->where(['utilizador_id' => $userId])
            ->andWhere(['<', 'data_visita', $hoje])
            ->with(['local', 'linhaReservas', 'linhaReservas.tipoBilhete'])
            ->orderBy(['data_criacao' => SORT_DESC])
            ->all();
        
        $ticketsExpirados = [];
        foreach ($reservasExpiradas as $reserva) {
            foreach ($reserva->linhaReservas as $linha) {
                for ($i = 1; $i <= $linha->quantidade; $i++) {
                    $ticketsExpirados[] = [
                        'reserva' => $reserva,
                        'linha' => $linha,
                        'ticketNumber' => $i,
                        'isExpirado' => true,
                    ];
                }
            }
        }
        
        $dataProviderExpiradas = new ArrayDataProvider([
            'allModels' => $ticketsExpirados,
            'pagination' => [
                'pageSize' => 6,
                'pageParam' => 'page-expirados',
            ],
        ]);
        
        return $this->render('index', [
            'dataProviderAtivas' => $dataProviderAtivas,
            'dataProviderExpiradas' => $dataProviderExpiradas,
        ]);
    }

    /**
     * Displays a single Reserva model.
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
     * Criar reserva (já está OK, só garantir autenticação)
     */

    public function actionCreate()
    {
        if (!$this->request->isPost) {
            return $this->redirect(['site/index']);
        }

        $postData = $this->request->post();

        // Se vem com 'confirmar' = 1, GRAVA na BD
        if (isset($postData['confirmar']) && $postData['confirmar'] == '1') {

            if (Yii::$app->user->isGuest) {
                Yii::$app->session->setFlash('error', 'Precisa de estar autenticado.');
                return $this->redirect(['site/login']);
            }

            try {
                $reserva = new Reserva();
                $reserva->GuardarReserva($postData);

                Yii::$app->session->setFlash('success', 'Reserva criada com sucesso!');
                return $this->redirect(['reserva/index']);
            } catch (\Exception $e) {
                // NÃO redirecionar - renderizar novamente com os dados
                Yii::$app->session->setFlash('error', $e->getMessage());

                try {
                    $dados = Reserva::obterDadosConfirmacao($postData);
                    return $this->render('create', $dados);
                } catch (\Exception $e2) {
                    Yii::$app->session->setFlash('error', $e2->getMessage());
                    return $this->redirect(['site/index']);
                }
            }
        }

        // Se NÃO tem 'confirmar', MOSTRA a página de prévia (create.php)
        try {
            $dados = Reserva::obterDadosConfirmacao($postData);
            return $this->render('create', $dados);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['site/index']);
        }
    }

    /**
     * Updates an existing Reserva model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Reserva model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Reserva model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Reserva the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reserva::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Downloads a ticket as PDF
     * @param int $id Reserva ID
     * @param int $linha_id LinhaReserva ID
     * @param int $ticket Ticket number
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDownloadTicket($id, $linha_id, $ticket)
    {
        $reserva = $this->findModel($id);
        
        // Verify the reservation belongs to the current user
        if ($reserva->utilizador_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('Não tem permissão para aceder a este bilhete.');
        }
        
        // Find the specific linha
        $linha = LinhaReserva::findOne(['id' => $linha_id, 'reserva_id' => $id]);
        if (!$linha) {
            throw new NotFoundHttpException('Bilhete não encontrado.');
        }
        
        // Validate ticket number
        $ticketNumber = (int)$ticket;
        if ($ticketNumber < 1 || $ticketNumber > $linha->quantidade) {
            throw new NotFoundHttpException('Número de bilhete inválido.');
        }
        
        // Generate ticket code
        $ticketCode = '#' . str_pad($reserva->id, 6, '0', STR_PAD_LEFT) . '-' . $ticketNumber;
        
        // Render PDF content
        $content = $this->renderPartial('_ticket-pdf', [
            'reserva' => $reserva,
            'linha' => $linha,
            'ticketNumber' => $ticketNumber,
            'ticketCode' => $ticketCode,
        ]);
        
        // Setup PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => [100, 180], // Custom size in mm (width x height) - reduced to fit content
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'filename' => "bilhete-{$ticketCode}.pdf",
            'content' => $content,
            'cssFile' => '@frontend/web/css/reservas/ticket-pdf.css',
            'marginTop' => 5,
            'marginBottom' => 5,
            'marginLeft' => 5,
            'marginRight' => 5,
            'options' => [
                'title' => "Bilhete {$ticketCode}",
            ],
            'methods' => [
                'SetAuthor' => 'Mais Lusitânia',
                'SetCreator' => 'Mais Lusitânia',
                'SetSubject' => 'Bilhete de Entrada',
            ]
        ]);
        
        return $pdf->render();
    }
}
