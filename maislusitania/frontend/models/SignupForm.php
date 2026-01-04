<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserProfile;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $primeiro_nome;
    public $ultimo_nome;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['primeiro_nome', 'trim'],
            ['primeiro_nome', 'required'],
            ['primeiro_nome', 'string', 'max' => 255],

            ['ultimo_nome', 'trim'],
            ['ultimo_nome', 'required'],
            ['ultimo_nome', 'string', 'max' => 255],

            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        //PASSAR STATUS PARA 10 (ATIVO)
        $user->status = 10;

        $user->save();

        //Atribuir role "user" ao novo utilizador
        $auth = Yii::$app->authManager;
        $userRole = $auth->getRole('user');
        $auth->assign($userRole, $user->getId());


        $userprofile = new UserProfile();
        $userprofile->user_id = $user->id;
        $userprofile->primeiro_nome = $this->primeiro_nome;
        $userprofile->ultimo_nome = $this->ultimo_nome;



        $userprofile->save();

        //return  $this->sendEmail($user);
        return true;
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
