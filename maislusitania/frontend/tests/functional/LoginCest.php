<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;

class LoginCest
{
    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
    }

    protected function formParams($login, $password)
    {
        return [
            'LoginForm[username]' => $login,
            'LoginForm[password]' => $password,
        ];
    }

    public function checkEmpty(FunctionalTester $I)
    {
        $I->submitForm('#login-form', $this->formParams('', ''));
        $I->seeValidationError('Username cannot be blank.');
        $I->seeValidationError('Password cannot be blank.');
    }

    public function checkWrongPassword(FunctionalTester $I)
    {
        $I->submitForm('#login-form', $this->formParams('mariofernandes', 'wrong'));
        $I->seeValidationError('Incorrect username or password.');
    }

    public function checkValidLogin(FunctionalTester $I)
    {
        $I->submitForm('#login-form', $this->formParams('mariofernandes', '12345678'));
        $I->dontSeeElement('#login-form');
        $I->see('Descubra o Património Histórico de Portugal');
        $I->seeElement('.user-avatar');
    }
}