<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\models\Reserva;
use common\models\LinhaReserva;
use common\models\User;
use common\models\LocalCultural;
use common\models\TipoBilhete;

class ReservasCest
{
    protected function login(FunctionalTester $I, string $role = 'user')
    {
        if ($role == 'sem bilhetes') {
            $I->amOnPage('/site/login');
            $I->submitForm('#login-form', [
                'LoginForm[username]' => 'tester',
                'LoginForm[password]' => '12345678',
            ]);
        } 
        else  {
            $I->amOnPage('/site/login');
            $I->submitForm('#login-form', [
                'LoginForm[username]' => 'mariofernandes',
                'LoginForm[password]' => '12345678',
            ]);
        }
    }

    public function testGuestCannotAccessReservaIndex(FunctionalTester $I)
    {
        $I->amOnPage('/reserva/index');
        $I->seeInCurrentUrl('/site/login');
    }

    public function testAuthenticatedUserCanAccessReservaIndex(FunctionalTester $I)
    {
        $this->login($I);
        $I->amOnPage('/reserva/index');
        $I->see('Meus Bilhetes');
    }

    public function testEmptyStateDisplayedWhenNoReservations(FunctionalTester $I)
    {
        $this->login($I, 'sem bilhetes');
        $I->amOnPage('/reserva/index');
        $I->see('Ainda não tem bilhetes');
    }

    public function testExpiredReservationsDisplayed(FunctionalTester $I)
    {
        $this->login($I);
        
        $user = User::findOne(['username' => 'mariofernandes']);
        if (!$user) {
            $I->comment('Usuário não encontrado no banco de testes');
            return;
        }
        
        $local = LocalCultural::find()->one();
        if (!$local) {
            $I->comment('Nenhum local cultural encontrado no banco de testes');
            return;
        }
        
        $tipoBilhete = TipoBilhete::find()->where(['local_id' => $local->id])->one();
        if (!$tipoBilhete) {
            $I->comment('Nenhum tipo de bilhete encontrado');
            return;
        }
        
        $reserva = new Reserva();
        $reserva->utilizador_id = $user->id;
        $reserva->local_id = $local->id;
        $reserva->data_visita = date('Y-m-d', strtotime('-7 days'));
        $reserva->preco_total = 10.00;
        $reserva->estado = Reserva::ESTADO_CONFIRMADA;
        $reserva->save(false);

        $linha = new LinhaReserva();
        $linha->reserva_id = $reserva->id;
        $linha->tipo_bilhete_id = $tipoBilhete->id;
        $linha->quantidade = 1;
        $linha->save(false);

        $I->amOnPage('/reserva/index');
        $I->see('Bilhetes Expirados');
    }

    public function testActiveReservationsDisplayed(FunctionalTester $I)
    {
        $this->login($I);
        
        $user = User::findOne(['username' => 'mariofernandes']);
        if (!$user) {
            $I->comment('Usuário não encontrado no banco de testes');
            return;
        }
        
        $local = LocalCultural::find()->one();
        if (!$local) {
            $I->comment('Nenhum local cultural encontrado no banco de testes');
            return;
        }
        
        $tipoBilhete = TipoBilhete::find()->where(['local_id' => $local->id])->one();
        if (!$tipoBilhete) {
            $I->comment('Nenhum tipo de bilhete encontrado');
            return;
        }
        
        $reserva = new Reserva();
        $reserva->utilizador_id = $user->id;
        $reserva->local_id = $local->id;
        $reserva->data_visita = date('Y-m-d', strtotime('+7 days'));
        $reserva->preco_total = 15.00;
        $reserva->estado = Reserva::ESTADO_CONFIRMADA;
        $reserva->save(false);

        $linha = new LinhaReserva();
        $linha->reserva_id = $reserva->id;
        $linha->tipo_bilhete_id = $tipoBilhete->id;
        $linha->quantidade = 2;
        $linha->save(false);

        $I->amOnPage('/reserva/index');
        $I->see('Bilhetes Ativos');
        $I->see($local->nome);
    }

    public function testReservationDetailsDisplayed(FunctionalTester $I)
    {
        $this->login($I);
        
        $user = User::findOne(['username' => 'mariofernandes']);
        if (!$user) {
            $I->comment('Usuário não encontrado no banco de testes');
            return;
        }
        
        $local = LocalCultural::find()->one();
        if (!$local) {
            $I->comment('Nenhum local cultural encontrado no banco de testes');
            return;
        }
        
        $tipoBilhete = TipoBilhete::find()->where(['local_id' => $local->id])->one();
        if (!$tipoBilhete) {
            $I->comment('Nenhum tipo de bilhete encontrado');
            return;
        }
        
        $reserva = new Reserva();
        $reserva->utilizador_id = $user->id;
        $reserva->local_id = $local->id;
        $reserva->data_visita = date('Y-m-d', strtotime('+3 days'));
        $reserva->preco_total = 20.00;
        $reserva->estado = Reserva::ESTADO_CONFIRMADA;
        $reserva->save(false);

        $linha = new LinhaReserva();
        $linha->reserva_id = $reserva->id;
        $linha->tipo_bilhete_id = $tipoBilhete->id;
        $linha->quantidade = 1;
        $linha->save(false);

        $I->amOnPage('/reserva/index');
        $I->see($local->nome);
        $I->see('20,00 €');
        $I->see(date('d/m/Y', strtotime('+3 days')));
    }
}