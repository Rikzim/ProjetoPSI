<?php
namespace backend\modules\api\controllers;

use yii\rest\Controller;
use Yii;
use common\models\User;
use yii\filters\Cors;

class LoginFormController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['POST','OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');


        // Valida se campos estão vazios
        if (empty($username) || empty($password)) {
            Yii::$app->response->statusCode = 400;
            return ['status' => 'error', 'message' => 'Username ou password vazios'];
        }

        // Procura o utilizador
        $user = User::findOne(['username' => $username]);

        if($user->status != User::STATUS_ACTIVE) {
            Yii::$app->response->statusCode = 403;
            return ['status' => 'error', 'message' => 'Utilizador inativo'];
        }

        if (!$user) {
            Yii::$app->response->statusCode = 404;
            return ['status' => 'error', 'message' => 'Utilizador não encontrado'];
        }

        // Compara os hashes diretamente
        if (!Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)) {
            Yii::$app->response->statusCode = 401;
            return ['status' => 'error', 'message' => 'Palavra-passe incorreta'];
        }

        // Retorna o token
        if (empty($user->auth_key)) {
            $user->generateAuthKey();
            $user->save(false);
        }

        return [
            'username' => $user->username,
            'user_id' => $user->id,
            'auth_key' => $user->auth_key,
        ];
    }
}