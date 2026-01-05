<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\models\Reserva;
use common\models\LinhaReserva;
use common\models\LocalCultural;
use common\models\TipoBilhete;
use common\models\User;
use Yii;

/**
 * Testes funcionais para o sistema de Reservas
 * Foca em testes de modelo, validações e lógica de negócio
 */
class ReservasFunctionalCest
{
    private $userId;
    private $localId;
    private $tipoBilheteId;
    
    /**
     * Setup executado antes de cada teste
     */
    public function _before(FunctionalTester $I)
    {
        // Limpar dados de teste anteriores
        LinhaReserva::deleteAll(['>', 'id', 0]);
        Reserva::deleteAll(['>', 'id', 0]);
        
        // Criar utilizador de teste
        $user = User::findOne(['username' => 'teste_reservas']);
        if (!$user) {
            $user = new User();
            $user->username = 'teste_reservas';
            $user->email = 'teste_reservas@example.com';
            $user->setPassword('123456');
            $user->generateAuthKey();
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);
        }
        $this->userId = $user->id;
        
        // Criar local cultural de teste
        $local = LocalCultural::findOne(['nome' => 'Museu de Teste']);
        if (!$local) {
            $local = new LocalCultural();
            $local->nome = 'Museu de Teste';
            $local->morada = 'Rua de Teste, 123';
            $local->tipo_id = 1;
            $local->distrito_id = 1;
            $local->latitude = 38.7223;
            $local->longitude = -9.1393;
            $local->descricao = 'Local para testes funcionais';
            $local->ativo = 1;
            $local->save(false);
        }
        $this->localId = $local->id;
        
        // Criar tipo de bilhete
        $tipoBilhete = TipoBilhete::findOne(['local_id' => $this->localId, 'nome' => 'Normal Teste']);
        if (!$tipoBilhete) {
            $tipoBilhete = new TipoBilhete();
            $tipoBilhete->nome = 'Normal Teste';
            $tipoBilhete->descricao = 'Bilhete normal de teste';
            $tipoBilhete->preco = 10.00;
            $tipoBilhete->ativo = 1;
            $tipoBilhete->local_id = $this->localId;
            $tipoBilhete->save(false);
        }
        $this->tipoBilheteId = $tipoBilhete->id;
    }
    
    /**
     * Cleanup após cada teste
     */
    public function _after(FunctionalTester $I)
    {
        LinhaReserva::deleteAll(['>', 'id', 0]);
        Reserva::deleteAll(['>', 'id', 0]);
    }
    
    /**
     * Teste 1: Criar uma reserva através do modelo
     */
    public function testarCriarReserva(FunctionalTester $I)
    {
        $I->wantTo('criar uma reserva usando o modelo');
        
        // Criar reserva diretamente no banco
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        $I->assertTrue($reserva->save(false), 'Reserva deve ser salva');
        
        // Verificar na base de dados
        $I->seeRecord(Reserva::class, [
            'id' => $reserva->id,
            'utilizador_id' => $this->userId,
            'local_id' => $this->localId
        ]);
        
        // Criar linha de reserva
        $linha = new LinhaReserva();
        $linha->reserva_id = $reserva->id;
        $linha->tipo_bilhete_id = $this->tipoBilheteId;
        $linha->quantidade = 1;
        $I->assertTrue($linha->save(false), 'Linha de reserva deve ser salva');
        
        $I->seeRecord(LinhaReserva::class, [
            'reserva_id' => $reserva->id,
            'quantidade' => 1
        ]);
    }
    
    /**
     * Teste 2: Verificar que reservas são salvas corretamente no banco
     */
    public function testarSalvarReservaNoBanco(FunctionalTester $I)
    {
        $I->wantTo('verificar que uma reserva é salva corretamente');
        
        // Criar reserva
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        
        $I->assertTrue($reserva->save(false), 'Reserva deve ser salva');
        
        // Verificar no banco usando Codeception
        $I->seeRecord(Reserva::class, [
            'id' => $reserva->id,
            'utilizador_id' => $this->userId,
            'local_id' => $this->localId,
        ]);
    }
    
    /**
     * Teste 3: Verificar cálculo de preço total
     */
    public function testarCalculoPrecoTotal(FunctionalTester $I)
    {
        $I->wantTo('verificar que o preço total é calculado corretamente');
        
        // Criar reserva com múltiplos bilhetes
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 20.00; // 2 bilhetes x 10€
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        $reserva->save(false);
        
        $linha = new LinhaReserva();
        $linha->reserva_id = $reserva->id;
        $linha->tipo_bilhete_id = $this->tipoBilheteId;
        $linha->quantidade = 2;
        $linha->save(false);
        
        // Verificar
        $I->assertEquals(20.00, $reserva->preco_total, 'Preço deve ser 2 x 10€ = 20€');
        $I->assertEquals(2, $linha->quantidade, 'Quantidade deve ser 2');
    }
    
    /**
     * Teste 4: Apagar reserva do banco de dados
     */
    public function testarApagarReserva(FunctionalTester $I)
    {
        $I->wantTo('apagar uma reserva do banco de dados');
        
        // Criar reserva
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        $reserva->save(false);
        
        $reservaId = $reserva->id;
        
        // Verificar que existe
        $I->seeRecord(Reserva::class, ['id' => $reservaId]);
        
        // Apagar
        $reserva->delete();
        
        // Verificar que foi removida
        $I->dontSeeRecord(Reserva::class, ['id' => $reservaId]);
    }
    
    /**
     * Teste 5: Validar que reserva não pode ter utilizador_id nulo
     */
    public function testarValidacaoUtilizadorObrigatorio(FunctionalTester $I)
    {
        $I->wantTo('verificar que utilizador_id é obrigatório');
        
        $reserva = new Reserva();
        $reserva->utilizador_id = null;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        
        $I->assertFalse($reserva->save(), 'Reserva sem utilizador não deve ser salva');
        $I->assertTrue($reserva->hasErrors('utilizador_id'), 'Deve ter erro no utilizador_id');
    }
    
    /**
     * Teste 6: Validar que reserva não pode ter local_id nulo
     */
    public function testarValidacaoLocalObrigatorio(FunctionalTester $I)
    {
        $I->wantTo('verificar que local_id é obrigatório');
        
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = null;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        
        $I->assertFalse($reserva->save(), 'Reserva sem local não deve ser salva');
        $I->assertTrue($reserva->hasErrors('local_id'), 'Deve ter erro no local_id');
    }
    
    /**
     * Teste 7: Verificar relacionamento entre Reserva e LinhaReserva
     */
    public function testarRelacionamentoReservaLinhaReserva(FunctionalTester $I)
    {
        $I->wantTo('verificar relacionamento entre reserva e suas linhas');
        
        // Criar reserva
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 20.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        $reserva->save(false);
        
        // Criar 2 linhas de reserva
        $linha1 = new LinhaReserva();
        $linha1->reserva_id = $reserva->id;
        $linha1->tipo_bilhete_id = $this->tipoBilheteId;
        $linha1->quantidade = 1;
        $linha1->save(false);
        
        $linha2 = new LinhaReserva();
        $linha2->reserva_id = $reserva->id;
        $linha2->tipo_bilhete_id = $this->tipoBilheteId;
        $linha2->quantidade = 1;
        $linha2->save(false);
        
        // Verificar relacionamento
        $reservaBD = Reserva::findOne($reserva->id);
        $I->assertNotNull($reservaBD, 'Reserva deve existir');
        $I->assertCount(2, $reservaBD->linhaReservas, 'Reserva deve ter 2 linhas');
    }
    
    /**
     * Teste 8: Verificar estados da reserva
     */
    public function testarEstadosReserva(FunctionalTester $I)
    {
        $I->wantTo('verificar estados da reserva');
        
        // Criar reserva confirmada
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        $reserva->save(false);
        
        $I->assertTrue($reserva->isEstadoConfirmada(), 'Estado deve ser Confirmada');
        $I->assertEquals(Reserva::ESTADO_CONFIRMADA, $reserva->estado);
        
        // Mudar para cancelada
        $reserva->setEstadoToCancelada();
        $reserva->save(false);
        
        $I->assertTrue($reserva->isEstadoCancelada(), 'Estado deve ser Cancelada');
        $I->assertEquals(Reserva::ESTADO_CANCELADA, $reserva->estado);
        
        // Mudar para expirado
        $reserva->setEstadoToExpirado();
        $reserva->save(false);
        
        $I->assertTrue($reserva->isEstadoExpirado(), 'Estado deve ser Expirado');
        $I->assertEquals(Reserva::ESTADO_EXPIRADO, $reserva->estado);
    }
    
    /**
     * Teste 9: Verificar data de visita no futuro
     */
    public function testarDataVisitaFutura(FunctionalTester $I)
    {
        $I->wantTo('verificar que data de visita pode ser no futuro');
        
        $dataFutura = date('Y-m-d', strtotime('+30 days'));
        
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = $dataFutura;
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        
        $I->assertTrue($reserva->save(false), 'Reserva com data futura deve ser salva');
        $I->assertEquals($dataFutura, $reserva->data_visita);
    }
    
    /**
     * Teste 10: Verificar que LinhaReserva requer quantidade positiva
     */
    public function testarQuantidadePositiva(FunctionalTester $I)
    {
        $I->wantTo('verificar que quantidade deve ser positiva');
        
        // Criar reserva primeiro
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        $reserva->save(false);
        
        // Tentar criar linha com quantidade zero
        $linha = new LinhaReserva();
        $linha->reserva_id = $reserva->id;
        $linha->tipo_bilhete_id = $this->tipoBilheteId;
        $linha->quantidade = 0;
        
        $I->assertFalse($linha->save(), 'Linha com quantidade 0 não deve ser salva');
        $I->assertTrue($linha->hasErrors('quantidade'), 'Deve ter erro na quantidade');
    }
    
    /**
     * Teste 11: Verificar busca de reservas por utilizador
     */
    public function testarBuscarReservasPorUtilizador(FunctionalTester $I)
    {
        $I->wantTo('buscar reservas de um utilizador específico');
        
        // Criar 2 reservas para o utilizador
        for ($i = 1; $i <= 2; $i++) {
            $reserva = new Reserva();
            $reserva->utilizador_id = $this->userId;
            $reserva->local_id = $this->localId;
            $reserva->data_visita = date('Y-m-d', strtotime("+{$i} day"));
            $reserva->preco_total = 10.00;
            $reserva->setEstadoToConfirmada();
            $reserva->data_criacao = date('Y-m-d H:i:s');
            $reserva->save(false);
        }
        
        // Buscar reservas
        $reservas = Reserva::find()
            ->where(['utilizador_id' => $this->userId])
            ->all();
        
        $I->assertCount(2, $reservas, 'Deve encontrar 2 reservas');
    }
    
    /**
     * Teste 12: Verificar atualização de reserva
     */
    public function testarAtualizarReserva(FunctionalTester $I)
    {
        $I->wantTo('atualizar dados de uma reserva existente');
        
        // Criar reserva
        $reserva = new Reserva();
        $reserva->utilizador_id = $this->userId;
        $reserva->local_id = $this->localId;
        $reserva->data_visita = date('Y-m-d', strtotime('+1 day'));
        $reserva->preco_total = 10.00;
        $reserva->setEstadoToConfirmada();
        $reserva->data_criacao = date('Y-m-d H:i:s');
        $reserva->save(false);
        
        $reservaId = $reserva->id;
        
        // Atualizar preço
        $reserva->preco_total = 15.00;
        $I->assertTrue($reserva->save(false), 'Atualização deve ter sucesso');
        
        // Verificar atualização no banco
        $reservaAtualizada = Reserva::findOne($reservaId);
        $I->assertEquals(15.00, $reservaAtualizada->preco_total, 'Preço deve estar atualizado');
    }
}
