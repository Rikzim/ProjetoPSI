<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;

class SignupCest
{
    protected $formId = '#form-signup';

    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/site/signup');
    }

    public function signupWithEmptyFields(FunctionalTester $I)
    {
        $I->submitForm($this->formId, []);
        $I->seeValidationError('Primeiro Nome cannot be blank.');
        $I->seeValidationError('Ultimo Nome cannot be blank.');
        $I->seeValidationError('Username cannot be blank.');
        $I->seeValidationError('Email cannot be blank.');
        $I->seeValidationError('Password cannot be blank.');
    }

    public function signupWithWrongEmail(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[primeiro_nome]' => 'Test',
            'SignupForm[ultimo_nome]' => 'User',
            'SignupForm[username]' => 'tester',
            'SignupForm[email]' => 'invalid-email',
            'SignupForm[password]' => 'tester_password',
        ]);
        $I->dontSee('Username cannot be blank.', '.invalid-feedback');
        $I->dontSee('Password cannot be blank.', '.invalid-feedback');
        $I->seeValidationError('Email is not a valid email address.');
    }

    public function signupWithShortPassword(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[primeiro_nome]' => 'Test',
            'SignupForm[ultimo_nome]' => 'User',
            'SignupForm[username]' => 'tester',
            'SignupForm[email]' => 'tester@example.com',
            'SignupForm[password]' => '123',
        ]);
        $I->seeValidationError('Password should contain at least');
    }

    public function signupWithExistingUsername(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[primeiro_nome]' => 'Test',
            'SignupForm[ultimo_nome]' => 'User',
            'SignupForm[username]' => 'joaomatias',
            'SignupForm[email]' => 'newemail@example.com',
            'SignupForm[password]' => 'tester_password',
        ]);
        $I->seeValidationError('This username has already been taken.');
    }

    public function signupWithExistingEmail(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[primeiro_nome]' => 'Test',
            'SignupForm[ultimo_nome]' => 'User',
            'SignupForm[username]' => 'newuser',
            'SignupForm[email]' => 'joao.matias@example.com',
            'SignupForm[password]' => 'tester_password',
        ]);
        $I->seeValidationError('This email address has already been taken.');
    }

    public function signupSuccessfully(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[primeiro_nome]' => 'Test',
            'SignupForm[ultimo_nome]' => 'User',
            'SignupForm[username]' => 'newuser',
            'SignupForm[email]' => 'newuser@example.com',
            'SignupForm[password]' => 'secure_password123',
        ]);

        $I->seeRecord('common\models\User', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'status' => \common\models\User::STATUS_INACTIVE,
        ]);

        $I->seeEmailIsSent();
    }

    public function signupPageHasLoginLink(FunctionalTester $I)
    {
        $I->seeLink('Iniciar sessão');
    }
}
