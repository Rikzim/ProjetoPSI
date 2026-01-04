<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\LocalCulturalFixture;

class AvaliacaoCest
{
    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
            'local' => LocalCulturalFixture::class,
        ];
    }

    // ==================== TESTES COMO GUEST ====================

    public function testGuestPodeVerSecaoAvaliacoes(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/view?id=1');
        $I->see('Avaliações');
        $I->seeElement('.avaliacoes-section');
    }
    
    public function testGuestNaoVeFormularioAvaliacao(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/view?id=1');
        $I->dontSeeElement('.user-avaliacao-form');
    }

    // ==================== TESTES COMO UTILIZADOR LOGADO ====================

    public function testUtilizadorLogadoVeFormularioAvaliacao(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        $I->amOnPage('/local-cultural/view?id=1');
        $I->seeElement('.user-avaliacao-form');
        $I->seeElement('.star-rating');
        $I->seeElement('textarea[name="comentario"]');
    }

    public function testUtilizadorLogadoVeBotaoSubmit(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        $I->amOnPage('/local-cultural/view?id=1');
        $I->seeElement('.user-avaliacao-form button[type="submit"]');
        // O botão pode ser "Publicar" ou "Atualizar" dependendo se já existe avaliação
    }

    // ==================== TESTES DE CRIAÇÃO ====================

    public function testCriarAvaliacaoComComentario(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        $I->amOnPage('/local-cultural/view?id=1');
        $I->seeElement('.user-avaliacao-form');
        
        $I->selectOption('input[name="classificacao"]', '5');
        $I->fillField('comentario', 'Excelente local para visitar! Recomendo a todos.');
        $I->click('.user-avaliacao-form button[type="submit"]');
        
        // Após criar, deve redirecionar para a mesma página
        $I->seeInCurrentUrl('local-cultural/view');
    }

    public function testCriarAvaliacaoSemComentario(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        $I->amOnPage('/local-cultural/view?id=2');
        $I->seeElement('.user-avaliacao-form');
        
        $I->selectOption('input[name="classificacao"]', '4');
        $I->click('.user-avaliacao-form button[type="submit"]');
        
        $I->seeInCurrentUrl('local-cultural/view');
    }

    // ==================== TESTES DE EDIÇÃO ====================

    public function testFormularioMostraEditarQuandoTemAvaliacao(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        
        // Criar avaliação
        $I->amOnPage('/local-cultural/view?id=3');
        $I->selectOption('input[name="classificacao"]', '3');
        $I->fillField('comentario', 'Avaliação inicial para testar edição');
        $I->click('.user-avaliacao-form button[type="submit"]');
        
        // Voltar à página e verificar texto "Editar"
        $I->amOnPage('/local-cultural/view?id=3');
        $I->see('Editar a minha avaliação');
    }

    public function testBotaoAtualizarAparece(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        
        // Criar avaliação primeiro
        $I->amOnPage('/local-cultural/view?id=4');
        $I->selectOption('input[name="classificacao"]', '2');
        $I->click('.user-avaliacao-form button[type="submit"]');
        
        // Verificar botão Atualizar
        $I->amOnPage('/local-cultural/view?id=4');
        $I->see('Atualizar');
    }

    public function testEditarAvaliacao(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        
        // Criar avaliação
        $I->amOnPage('/local-cultural/view?id=5');
        $I->selectOption('input[name="classificacao"]', '2');
        $I->fillField('comentario', 'Primeira opinião');
        $I->click('.user-avaliacao-form button[type="submit"]');
        
        // Editar avaliação
        $I->amOnPage('/local-cultural/view?id=5');
        $I->selectOption('input[name="classificacao"]', '5');
        $I->fillField('comentario', 'Mudei de opinião, é fantástico!');
        $I->click('.user-avaliacao-form button[type="submit"]');
        
        $I->seeInCurrentUrl('local-cultural/view');
    }

    // ==================== TESTES DE ELIMINAÇÃO ====================

    public function testBotaoEliminarAparece(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        
        // Criar avaliação
        $I->amOnPage('/local-cultural/view?id=6');
        $I->selectOption('input[name="classificacao"]', '4');
        $I->fillField('comentario', 'Para testar eliminação');
        $I->click('.user-avaliacao-form button[type="submit"]');
        
        // Verificar botão Eliminar
        $I->amOnPage('/local-cultural/view?id=6');
        $I->see('Eliminar');
        $I->seeElement('.btn-danger');
    }

    // ==================== TESTES DA LISTAGEM INDEX ====================

    public function testGuestPodeVerListagemLocais(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->see('Explore o Património de Portugal');
        $I->seeElement('.local-card');
    }

    public function testUtilizadorLogadoPodeVerListagemLocais(FunctionalTester $I)
    {
        $this->fazerLogin($I);
        $I->amOnPage('/local-cultural/index');
        $I->see('Explore o Património de Portugal');
        $I->seeElement('.local-card');
    }
    // ==================== MÉTODO AUXILIAR ====================

    private function fazerLogin(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'joaomatias',
            'LoginForm[password]' => '12345678',
        ]);
    }
}