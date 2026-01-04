<?php

namespace common\tests\Unit;

use common\models\Avaliacao;
use common\models\LocalCultural;
use common\models\User;
use common\fixtures\UserFixture;
use common\tests\UnitTester;
use yii\db\ActiveQuery;

class AvaliacaoTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    /**
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ];
    }

    protected function _before()
    {
    }

    // =====================================================
    // TESTES DE VALIDAÇÃO
    // =====================================================

    public function testValidationFailsWithEmptyRequiredFields()
    {
        $model = new Avaliacao();
        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('local_id', $model->errors);
        $this->assertArrayHasKey('utilizador_id', $model->errors);
        $this->assertArrayHasKey('classificacao', $model->errors);
    }

    public function testValidationFailsWithInvalidDataTypes()
    {
        $model = new Avaliacao([
            'local_id' => 'not-an-integer',
            'utilizador_id' => 'not-an-integer',
            'classificacao' => 'not-an-integer',
            'ativo' => 'not-an-integer',
        ]);

        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('local_id', $model->errors);
        $this->assertArrayHasKey('utilizador_id', $model->errors);
        $this->assertArrayHasKey('classificacao', $model->errors);
        $this->assertArrayHasKey('ativo', $model->errors);
    }

    public function testDefaultValues()
    {
        $model = new Avaliacao();
        $model->local_id = 1;
        $model->utilizador_id = 1;
        $model->classificacao = 5;
        $model->validate();

        // Verificar valores por defeito após validação
        $this->assertEquals(1, $model->ativo);
        $this->assertNull($model->comentario);
    }

    // =====================================================
    // TESTES DE CONFIGURAÇÃO DO MODELO
    // =====================================================

    public function testTableNameIsCorrect()
    {
        $this->assertEquals('avaliacao', Avaliacao::tableName());
    }

    public function testAttributeLabelsAreCorrect()
    {
        $model = new Avaliacao();
        $labels = $model->attributeLabels();

        $this->assertArrayHasKey('id', $labels);
        $this->assertArrayHasKey('local_id', $labels);
        $this->assertArrayHasKey('utilizador_id', $labels);
        $this->assertArrayHasKey('classificacao', $labels);
        $this->assertArrayHasKey('comentario', $labels);
        $this->assertArrayHasKey('data_avaliacao', $labels);
        $this->assertArrayHasKey('ativo', $labels);
        $this->assertEquals('Local', $labels['local_id']);
        $this->assertEquals('Utilizador', $labels['utilizador_id']);
    }

    public function testRulesAreNotEmpty()
    {
        $model = new Avaliacao();
        $rules = $model->rules();

        $this->assertIsArray($rules);
        $this->assertNotEmpty($rules);
    }

    // =====================================================
    // TESTES DE RELACIONAMENTOS
    // =====================================================

    public function testGetLocalReturnsActiveQuery()
    {
        $model = new Avaliacao();
        $this->assertInstanceOf(ActiveQuery::class, $model->getLocal());
    }

    public function testGetUtilizadorReturnsActiveQuery()
    {
        $model = new Avaliacao();
        $this->assertInstanceOf(ActiveQuery::class, $model->getUtilizador());
    }

    // =====================================================
    // TESTES DE INTEGRAÇÃO COM A BASE DE DADOS (CRUD)
    // =====================================================

    /**
     * Helper para criar um LocalCultural para os testes
     */
    private function criarLocalCultural()
    {
        $local = new LocalCultural([
            'nome' => 'Local Teste Avaliacao',
            'tipo_id' => 1,
            'morada' => 'Rua Teste',
            'distrito_id' => 1,
            'descricao' => 'Descrição teste',
            'latitude' => 40.0,
            'longitude' => -8.0,
        ]);
        $local->save();
        return $local;
    }

    public function testSaveAndFindAvaliacao()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        $avaliacao = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 5,
            'comentario' => 'Excelente local!',
            'ativo' => 1,
        ]);

        $this->assertTrue($avaliacao->save(), 'Avaliacao deveria ser guardada. Erros: ' . json_encode($avaliacao->errors));

        $foundAvaliacao = Avaliacao::findOne($avaliacao->id);
        $this->assertInstanceOf(Avaliacao::class, $foundAvaliacao);
        $this->assertEquals(5, $foundAvaliacao->classificacao);
        $this->assertEquals('Excelente local!', $foundAvaliacao->comentario);
    }

    public function testUpdateAvaliacao()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        $avaliacao = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 3,
            'comentario' => 'Razoável',
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao->save());

        $avaliacao->classificacao = 5;
        $avaliacao->comentario = 'Afinal é excelente!';
        $this->assertTrue($avaliacao->save());

        $foundAvaliacao = Avaliacao::findOne($avaliacao->id);
        $this->assertEquals(5, $foundAvaliacao->classificacao);
        $this->assertEquals('Afinal é excelente!', $foundAvaliacao->comentario);
    }

    public function testDeleteAvaliacao()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        $avaliacao = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 4,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao->save());
        $id = $avaliacao->id;

        $this->assertEquals(1, $avaliacao->delete());
        $this->assertNull(Avaliacao::findOne($id));
    }

    public function testAvaliacaoDuplicadaFalha()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        // Criar primeira avaliação
        $avaliacao1 = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 5,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao1->save());

        // Tentar criar avaliação duplicada (mesmo utilizador, mesmo local)
        $avaliacao2 = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 3,
            'ativo' => 1,
        ]);
        $this->assertFalse($avaliacao2->save());
    }

    public function testAvaliacaoRelacionamentoComUtilizador()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        $avaliacao = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 4,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao->save());

        $avaliacao = Avaliacao::findOne($avaliacao->id);
        $this->assertInstanceOf(User::class, $avaliacao->utilizador);
        $this->assertEquals($user['username'], $avaliacao->utilizador->username);
    }

    public function testAvaliacaoRelacionamentoComLocal()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        $avaliacao = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 4,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao->save());

        $avaliacao = Avaliacao::findOne($avaliacao->id);
        $this->assertInstanceOf(LocalCultural::class, $avaliacao->local);
        $this->assertEquals('Local Teste Avaliacao', $avaliacao->local->nome);
    }

    public function testAvaliacaoSemComentario()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        $avaliacao = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 5,
            'ativo' => 1,
        ]);

        $this->assertTrue($avaliacao->save(), 'Avaliação sem comentário deveria ser guardada.');
        $this->assertNull($avaliacao->comentario);
    }

    public function testLocalPodeTerMultiplasAvaliacoes()
    {
        $user1 = $this->tester->grabFixture('user', 0);
        $user2 = $this->tester->grabFixture('user', 1);
        $local = $this->criarLocalCultural();

        // Contar avaliações existentes para este local antes de adicionar novas
        $countBefore = Avaliacao::find()->where(['local_id' => $local->id])->count();

        // Dois utilizadores diferentes avaliam o mesmo local
        $avaliacao1 = new Avaliacao([
            'utilizador_id' => $user1['id'],
            'local_id' => $local->id,
            'classificacao' => 5,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao1->save());

        $avaliacao2 = new Avaliacao([
            'utilizador_id' => $user2['id'],
            'local_id' => $local->id,
            'classificacao' => 4,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao2->save());

        // Verificar que o local tem duas avaliações novas
        $avaliacoes = Avaliacao::find()->where(['local_id' => $local->id])->all();
        $this->assertCount($countBefore + 2, $avaliacoes);
    }

    public function testUtilizadorPodeAvaliarMultiplosLocais()
    {
        $user = $this->tester->grabFixture('user', 0);

        // Criar dois locais diferentes
        $local1 = $this->criarLocalCultural();
        $local2 = new LocalCultural([
            'nome' => 'Segundo Local Avaliacao',
            'tipo_id' => 1,
            'morada' => 'Rua Teste 2',
            'distrito_id' => 1,
            'descricao' => 'Descrição teste 2',
            'latitude' => 41.0,
            'longitude' => -7.0,
        ]);
        $local2->save();

        // Utilizador avalia ambos os locais
        $avaliacao1 = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local1->id,
            'classificacao' => 5,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao1->save());

        $avaliacao2 = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local2->id,
            'classificacao' => 3,
            'ativo' => 1,
        ]);
        $this->assertTrue($avaliacao2->save());

        // Verificar que o utilizador tem duas avaliações
        $avaliacoes = Avaliacao::find()->where(['utilizador_id' => $user['id']])->all();
        $this->assertCount(2, $avaliacoes);
    }

    public function testAvaliacaoInativa()
    {
        $user = $this->tester->grabFixture('user', 0);
        $local = $this->criarLocalCultural();

        $avaliacao = new Avaliacao([
            'utilizador_id' => $user['id'],
            'local_id' => $local->id,
            'classificacao' => 5,
            'ativo' => 0,
        ]);

        $this->assertTrue($avaliacao->save());

        $foundAvaliacao = Avaliacao::findOne($avaliacao->id);
        $this->assertEquals(0, $foundAvaliacao->ativo);
    }
}
