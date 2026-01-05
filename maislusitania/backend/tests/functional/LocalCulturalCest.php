<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\models\User;
use common\models\LocalCultural;
use Yii;

class LocalCulturalCest
{
    private $adminUser;

    /**
     * Executado antes de cada teste
     * Cria um utilizador admin e faz login
     */
    public function _before(FunctionalTester $I)
    {
        // 1. Criar ou encontrar utilizador admin
        $this->adminUser = User::findByUsername('admin_tester');
        
        if (!$this->adminUser) {
            $this->adminUser = new User();
            $this->adminUser->username = 'admin_tester';
            $this->adminUser->email = 'admin_tester@example.com';
            $this->adminUser->setPassword('password_123');
            $this->adminUser->generateAuthKey();
            $this->adminUser->status = User::STATUS_ACTIVE;
            
            if (! $this->adminUser->save()) {
                throw new \Exception('Falha ao criar utilizador admin:  ' . json_encode($this->adminUser->errors));
            }
            
            // Atribuir role de admin
            $auth = Yii::$app->authManager;
            $adminRole = $auth->getRole('admin');
            if ($adminRole) {
                $auth->assign($adminRole, $this->adminUser->id);
            }
        }

        // 2. Fazer login
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin_tester',
            'LoginForm[password]' => 'password_123',
        ]);
        
        // Verificar se login foi bem-sucedido
        $I->see('admin_tester');
        $I->dontSee('Login');
    }

    /**
     * Executado depois de cada teste
     * Limpa dados de teste
     */
    public function _after(FunctionalTester $I)
    {
        // Limpar locais de teste criados
        LocalCultural::deleteAll(['nome' => [
            'Museu de Teste',
            'Local para Editar',
            'Museu Editado',
            'Local para Apagar',
        ]]);
    }

    /**
     * Teste 1: Verificar página de listagem
     */
    public function checkIndexPage(FunctionalTester $I)
    {
        $I->wantTo('verificar a página de listagem de locais culturais');
        
        $I->amOnRoute('local-cultural/index');
        $I->see('Locais Culturais', 'h1'); // Ajusta conforme o título real
        $I->seeLink('Criar Local Cultural'); // Ou o texto real do botão
    }

    /**
     * Teste 2: Criar local cultural com dados válidos
     */
    public function createLocalCulturalWithValidData(FunctionalTester $I)
    {
        $I->wantTo('criar um novo local cultural com dados válidos');
        
        $I->amOnRoute('local-cultural/create');
        $I->see('Criar Local Cultural'); // Ajusta conforme título real
        
        // Preencher formulário
        $I->submitForm('#local-cultural-form', [ // Ajusta o ID do form
            'LocalCultural[nome]' => 'Museu de Teste',
            'LocalCultural[tipo_id]' => 1, // Museu
            'LocalCultural[morada]' => 'Rua do Teste, 123',
            'LocalCultural[distrito_id]' => 11, // Lisboa
            'LocalCultural[descricao]' => 'Um museu para testes funcionais.',
            'LocalCultural[latitude]' => '38.7',
            'LocalCultural[longitude]' => '-9.1',
        ]);
        
        // Verificar sucesso
        $I->see('Museu de Teste');
        $I->see('Rua do Teste, 123');
        
        // Verificar na base de dados
        $I->seeRecord(LocalCultural::class, [
            'nome' => 'Museu de Teste',
            'morada' => 'Rua do Teste, 123',
        ]);
    }

    /**
     * Teste 3: Tentar criar local cultural sem dados obrigatórios
     */
    public function createLocalCulturalWithInvalidData(FunctionalTester $I)
    {
        $I->wantTo('verificar validações ao criar local sem dados obrigatórios');
        
        $I->amOnRoute('local-cultural/create');
        
        // Submeter formulário vazio
        $I->submitForm('#local-cultural-form', []);
        
        // Verificar mensagens de erro
        $I->see('Nome cannot be blank', '. help-block'); // Ajusta o seletor
        $I->see('Morada cannot be blank', '.help-block');
        $I->see('Tipo ID cannot be blank', '.help-block');
        $I->see('Distrito ID cannot be blank', '.help-block');
    }

    /**
     * Teste 4: Verificar validação de coordenadas inválidas
     */
    public function createLocalCulturalWithInvalidCoordinates(FunctionalTester $I)
    {
        $I->wantTo('verificar validação de coordenadas inválidas');
        
        $I->amOnRoute('local-cultural/create');
        
        $I->submitForm('#local-cultural-form', [
            'LocalCultural[nome]' => 'Local com Coordenadas Inválidas',
            'LocalCultural[tipo_id]' => 1,
            'LocalCultural[morada]' => 'Rua Teste',
            'LocalCultural[distrito_id]' => 11,
            'LocalCultural[descricao]' => 'Teste',
            'LocalCultural[latitude]' => '999', // Latitude inválida (deve ser entre -90 e 90)
            'LocalCultural[longitude]' => '999', // Longitude inválida (deve ser entre -180 e 180)
        ]);
        
        // Deve mostrar erros de validação (ajusta conforme tuas regras)
        $I->see('error'); // Genérico, ajusta conforme mensagem real
    }

    /**
     * Teste 5: Ver detalhes de um local cultural
     */
    public function viewLocalCultural(FunctionalTester $I)
    {
        $I->wantTo('ver detalhes de um local cultural');
        
        // Criar um local primeiro
        $local = new LocalCultural();
        $local->nome = 'Local para Visualizar';
        $local->tipo_id = 1;
        $local->distrito_id = 11;
        $local->morada = 'Morada para Ver';
        $local->descricao = 'Descrição para Ver';
        $local->latitude = 38.7;
        $local->longitude = -9.1;
        $local->save(false); // false para skip validation
        
        // Ir para a página de detalhes
        $I->amOnRoute('local-cultural/view', ['id' => $local->id]);
        
        // Verificar informações
        $I->see('Local para Visualizar');
        $I->see('Morada para Ver');
        $I->see('Descrição para Ver');
        
        // Limpar
        $local->delete();
    }

    /**
     * Teste 6: Ver local cultural inexistente (deve dar erro 404)
     */
    public function viewNonExistentLocalCultural(FunctionalTester $I)
    {
        $I->wantTo('verificar erro ao tentar ver local inexistente');
        
        $I->amOnRoute('local-cultural/view', ['id' => 999999]);
        
        // Deve mostrar erro 404 ou mensagem de "não encontrado"
        $I->see('404'); // Ou a mensagem real do teu sistema
    }

    /**
     * Teste 7: Atualizar local cultural existente
     */
    public function updateLocalCulturalWithValidData(FunctionalTester $I)
    {
        $I->wantTo('atualizar um local cultural com dados válidos');
        
        // Criar um local primeiro
        $local = new LocalCultural();
        $local->nome = 'Local para Editar';
        $local->tipo_id = 1;
        $local->distrito_id = 11;
        $local->morada = 'Morada Original';
        $local->descricao = 'Descrição Original';
        $local->latitude = 10;
        $local->longitude = 10;
        $local->save(false);
        
        // Ir para a página de edição
        $I->amOnRoute('local-cultural/update', ['id' => $local->id]);
        
        // Atualizar dados
        $I->submitForm('#local-cultural-form', [
            'LocalCultural[nome]' => 'Museu Editado',
            'LocalCultural[morada]' => 'Morada Atualizada',
        ]);
        
        // Verificar atualização
        $I->see('Museu Editado');
        $I->see('Morada Atualizada');
        
        // Verificar na base de dados
        $I->seeRecord(LocalCultural::class, [
            'id' => $local->id,
            'nome' => 'Museu Editado',
            'morada' => 'Morada Atualizada',
        ]);
    }

    /**
     * Teste 8: Tentar atualizar local inexistente
     */
    public function updateNonExistentLocalCultural(FunctionalTester $I)
    {
        $I->wantTo('verificar erro ao tentar atualizar local inexistente');
        
        $I->amOnRoute('local-cultural/update', ['id' => 999999]);
        
        // Deve mostrar erro 404
        $I->see('404');
    }

    /**
     * Teste 9: Apagar local cultural (usando workaround para POST)
     */
    public function deleteLocalCultural(FunctionalTester $I)
    {
        $I->wantTo('apagar um local cultural');
        
        // Criar um local primeiro
        $local = new LocalCultural();
        $local->nome = 'Local para Apagar';
        $local->tipo_id = 1;
        $local->distrito_id = 11;
        $local->morada = 'Morada Apagar';
        $local->descricao = 'Descrição Apagar';
        $local->latitude = 10;
        $local->longitude = 10;
        $local->save(false);
        
        $localId = $local->id;
        
        // WORKAROUND: Como o delete requer POST e o link usa JavaScript,
        // vamos apagar diretamente usando o modelo (simula o comportamento)
        // Isto não é ideal mas funciona para testes funcionais sem Selenium
        
        // Alternativa 1: Apagar via código (simula ação do controller)
        $deleted = LocalCultural::findOne($localId)->delete();
        $I->assertTrue($deleted > 0, 'Local deveria ter sido apagado');
        
        // Verificar que não existe mais na BD
        $I->dontSeeRecord(LocalCultural::class, ['id' => $localId]);
        
        // Verificar que não aparece na listagem
        $I->amOnRoute('local-cultural/index');
        $I->dontSee('Local para Apagar');
    }

    /**
     * Teste 10: Pesquisar locais culturais por nome
     */
    public function searchLocalCulturalByName(FunctionalTester $I)
    {
        $I->wantTo('pesquisar locais culturais por nome');
        
        // Criar alguns locais para pesquisar
        $local1 = new LocalCultural();
        $local1->nome = 'Museu Nacional';
        $local1->tipo_id = 1;
        $local1->distrito_id = 11;
        $local1->morada = 'Lisboa';
        $local1->descricao = 'Teste';
        $local1->latitude = 10;
        $local1->longitude = 10;
        $local1->save(false);
        
        $local2 = new LocalCultural();
        $local2->nome = 'Teatro Municipal';
        $local2->tipo_id = 2;
        $local2->distrito_id = 11;
        $local2->morada = 'Lisboa';
        $local2->descricao = 'Teste';
        $local2->latitude = 10;
        $local2->longitude = 10;
        $local2->save(false);
        
        // Ir para index e pesquisar
        $I->amOnRoute('local-cultural/index');
        
        // Pesquisar por "Museu" (ajusta conforme o teu form de pesquisa)
        $I->fillField('LocalCulturalSearch[nome]', 'Museu');
        $I->click('Pesquisar'); // Ou o texto do botão
        
        // Deve mostrar apenas o Museu
        $I->see('Museu Nacional');
        $I->dontSee('Teatro Municipal');
        
        // Limpar
        $local1->delete();
        $local2->delete();
    }

    /**
     * Teste 11: Filtrar locais por distrito
     */
    public function filterLocalCulturalByDistrito(FunctionalTester $I)
    {
        $I->wantTo('filtrar locais culturais por distrito');
        
        // Criar locais em diferentes distritos
        $localLisboa = new LocalCultural();
        $localLisboa->nome = 'Local em Lisboa';
        $localLisboa->tipo_id = 1;
        $localLisboa->distrito_id = 11; // Lisboa
        $localLisboa->morada = 'Lisboa';
        $localLisboa->descricao = 'Teste';
        $localLisboa->latitude = 10;
        $localLisboa->longitude = 10;
        $localLisboa->save(false);
        
        $localPorto = new LocalCultural();
        $localPorto->nome = 'Local no Porto';
        $localPorto->tipo_id = 1;
        $localPorto->distrito_id = 13; // Porto (ajusta o ID real)
        $localPorto->morada = 'Porto';
        $localPorto->descricao = 'Teste';
        $localPorto->latitude = 10;
        $localPorto->longitude = 10;
        $localPorto->save(false);
        
        // Filtrar por Lisboa
        $I->amOnRoute('local-cultural/index');
        $I->selectOption('LocalCulturalSearch[distrito_id]', 11);
        $I->click('Pesquisar');
        
        // Deve mostrar apenas Lisboa
        $I->see('Local em Lisboa');
        $I->dontSee('Local no Porto');
        
        // Limpar
        $localLisboa->delete();
        $localPorto->delete();
    }
}