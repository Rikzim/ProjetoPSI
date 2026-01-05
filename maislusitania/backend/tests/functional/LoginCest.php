<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;

/**
 * Class LoginCest
 */
class LoginCest
{
    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

    /**
     * @param FunctionalTester $I
     */
    public function testLoginPageIsAccessible(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->see('Login');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testLoginWithValidCredentials(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'joaomatias',
            'LoginForm[password]' => '12345678',
        ]);

        $I->dontSee('Login');
        $I->dontSeeElement('#login-form');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testLoginWithInvalidCredentials(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'invalid_user',
            'LoginForm[password]' => 'wrong_password',
        ]);

        $I->seeElement('#login-form');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testLoginWithEmptyFields(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => '',
            'LoginForm[password]' => '',
        ]);

        $I->seeElement('#login-form');
        $I->see('cannot be blank');
    }

    /**
     * Test that a user without backend permission cannot access backend
     * @param FunctionalTester $I
     */
    public function testLoginWithoutBackendPermission(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'clientejoao',
            'LoginForm[password]' => '12345678',
        ]);

        // User without admin/gestor role should not be able to access backend
        $I->amOnPage('/site/index');
        $I->seeInCurrentUrl('login');
    }
}
