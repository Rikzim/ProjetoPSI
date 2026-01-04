<?php
namespace backend\modules\api\controllers;

use Yii;
use common\models\UserProfile;
use common\models\User;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserProfileController extends ActiveController
{
    // ========================================
    // Define o modelo
    // ========================================
    public $modelClass = 'common\models\UserProfile';

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
    // ENDPOINT: Obter perfil do usuário autenticado
    // ========================================
    /**
     * Retorna o perfil do usuário autenticado
     * Endpoint: GET /api/user-profile/me?access-token=SEU_TOKEN
     * 
     * @return UserProfile|array
     */
    public function actionMe()
    {
        // Obtém o ID do usuário autenticado
        $user = Yii::$app->user->identity;
        
        // Busca o perfil do usuário autenticado
        $userProfile = UserProfile::findOne(['user_id' => $user->id]);
        
        // Se o perfil não existir, retorna erro 404
        if (!$userProfile) {
            Yii::$app->response->statusCode = 404;
            return [
                'success' => false,
                'message' => 'Perfil não encontrado para este usuário.',
            ];
        }
        
        //Retorna o perfil com dados do user

        $data = array_map(function($userProfile) {
            return [
                'id' => $userProfile->id,
                'primeiro_nome' => $userProfile->primeiro_nome,
                'ultimo_nome' => $userProfile->ultimo_nome,
                'imagem_perfil' => $userProfile->getImageAPI(), // URL completa da imagem
                'user_id' => $userProfile->user_id,
                'username' => $userProfile->user->username, // Dados do user relacionado
                'email' => $userProfile->user->email,
                'data_adesao' => Yii::$app->formatter->asDate($userProfile->user->created_at),
                 
            ];
        }, [$userProfile]);
        return $data;
    }

    // ========================================
    // ENDPOINT: Atualizar perfil do utilizador autenticado
    // ========================================
    /**
     * Atualiza o perfil do utilizador autenticado
     * Endpoint: PUT/POST /api/user-profile/update-profile?access-token=SEU_TOKEN
     * Body: { "primeiro_nome": "...", "ultimo_nome": "...", "username": "..." }
     * 
     * @return array
     */
    public function actionUpdateProfile()
    {
        $user = Yii::$app->user->identity;
        
        $userProfile = UserProfile::findOne(['user_id' => $user->id]);
        
        if (!$userProfile) {
            throw new NotFoundHttpException('Perfil não encontrado para este utilizador.');
        }
        
        // Carrega os dados do body da requisição
        $params = Yii::$app->request->bodyParams;
        
        // Atualiza campos do perfil
        if (isset($params['primeiro_nome'])) {
            $userProfile->primeiro_nome = $params['primeiro_nome'];
        }
        if (isset($params['ultimo_nome'])) {
            $userProfile->ultimo_nome = $params['ultimo_nome'];
        }
        
        // Atualiza username do User
        $userUpdated = true;
        if (isset($params['username'])) {
            $newUsername = trim($params['username']);
            
            // Verifica se o username já existe (excluindo o próprio user)
            $existingUser = User::find()
                ->where(['username' => $newUsername])
                ->andWhere(['!=', 'id', $user->id])
                ->one();
            
            if ($existingUser) {
                Yii::$app->response->statusCode = 422;
                return [
                    'success' => false,
                    'message' => 'Este username já está em uso.',
                ];
            }
            
            $user->username = $newUsername;
            $userUpdated = $user->save(false);
        }
        
        if ($userProfile->save() && $userUpdated) {
            return [
                'success' => true,
                'message' => 'Perfil atualizado com sucesso.',
                'data' => [
                    'id' => $userProfile->id,
                    'primeiro_nome' => $userProfile->primeiro_nome,
                    'ultimo_nome' => $userProfile->ultimo_nome,
                    'user_id' => $userProfile->user_id,
                    'username' => $user->username,
                ],
            ];
        }
        
        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            'message' => 'Erro ao atualizar perfil.',
            'errors' => array_merge($userProfile->errors, $user->errors),
        ];
    }

    // ========================================
    // ENDPOINT: Alterar password do usuário autenticado
    // ========================================
    /**
     * Altera a password do usuário autenticado
     * Endpoint: POST /api/user-profile/change-password?access-token=SEU_TOKEN
     * Body: { "current_password": "senhaAtual", "new_password": "novaSenha", "confirm_password": "novaSenha" }
     * 
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionChangePassword()
    {
        $user = Yii::$app->user->identity;
        
        $params = Yii::$app->request->bodyParams;
        
        // Valida campos obrigatórios
        if (empty($params['current_password'])) {
            throw new BadRequestHttpException('A password atual é obrigatória.');
        }
        if (empty($params['new_password'])) {
            throw new BadRequestHttpException('A nova password é obrigatória.');
        }
        
        // Valida se a password atual está correta
        if (!$user->validatePassword($params['current_password'])) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => 'A password atual está incorreta.',
            ];
        }
                
        // Valida tamanho mínimo da password
        if (strlen($params['new_password']) < 6) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => 'A nova password deve ter pelo menos 6 caracteres.',
            ];
        }
        
        // Atualiza a password
        $user->setPassword($params['new_password']);
        
        if ($user->save(false)) {
            return [
                'success' => true,
                'message' => 'Password alterada com sucesso.',
            ];
        }
        
        Yii::$app->response->statusCode = 500;
        return [
            'success' => false,
            'message' => 'Erro ao alterar password.',
        ];
    }

    // ========================================
    // ENDPOINT: Eliminar conta do usuário autenticado (Soft Delete)
    // ========================================
    /**
     * Elimina a conta do usuário autenticado (Soft Delete)
     * Endpoint: DELETE /api/user-profile/delete-account?access-token=SEU_TOKEN
     * Body (opcional): { "password": "senhaAtual" } - para confirmação
     * 
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionDeleteAccount()
    {
        $user = Yii::$app->user->identity;
        
        $params = Yii::$app->request->bodyParams;
        
        // Valida password para confirmação (opcional mas recomendado)
        if (!empty($params['password'])) {
            if (!$user->validatePassword($params['password'])) {
                Yii::$app->response->statusCode = 400;
                return [
                    'success' => false,
                    'message' => 'Password incorreta. Não foi possível eliminar a conta.',
                ];
            }
        }
        
        // Realiza o soft delete usando o método existente no modelo User
        if ($user->SoftDelete()) {
            return [
                'success' => true,
                'message' => 'Conta eliminada com sucesso.',
            ];
        }
        
        Yii::$app->response->statusCode = 500;
        return [
            'success' => false,
            'message' => 'Erro ao eliminar conta.',
        ];
    }

    // ========================================
    // Controle de Acesso
    // ========================================
    public function checkAccess($action, $model = null, $params = [])
    {
        // Se o utilizador for admin (tiver permissão para gerir utilizadores), permite tudo
        if (Yii::$app->user->can('editUser')) {
            return;
        }

        // Se a ação for 'update' ou 'view', verifica se o perfil pertence ao utilizador logado
        if ($action === 'update' || $action === 'view' || $action === 'delete') {
            if ($model->user_id !== Yii::$app->user->id) {
                throw new ForbiddenHttpException('Não tem permissão para aceder a este perfil.');
            }
        }
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
                // Permite a utilizadores autenticados aceder ao seu próprio perfil
                [
                    'actions' => ['me'],
                    'allow' => true,
                    'roles' => ['viewOwnProfile'],
                ],
                // Permite a utilizadores autenticados atualizarem o seu próprio perfil
                [
                    'actions' => ['update', 'update-profile', 'change-password'],
                    'allow' => true,
                    'roles' => ['editOwnProfile'],
                ],
                // Permite a utilizadores autenticados eliminarem a sua própria conta
                [
                    'actions' => ['delete-account'],
                    'allow' => true,
                    'roles' => ['deleteOwnProfile'],
                ],
            ],
        ];

        // retornar em json
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;

        return $behaviors;
    } 
}