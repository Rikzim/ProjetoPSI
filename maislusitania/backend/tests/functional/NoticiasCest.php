<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\NoticiaFixture;
use common\fixtures\LocalCulturalFixture;

class NoticiasCest
{
    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
            'noticia' => NoticiaFixture::class,
            'local' => LocalCulturalFixture::class,
        ];
    }

    public function _before(FunctionalTester $I)
    {
        // Login como admin antes de cada teste
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'mariofernandes',
            'LoginForm[password]' => '12345678',
        ]);
    }

    public function testVisualizarListagemNoticias(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->see('Gestão de Notícias');
        $I->seeElement('.card-outline.card-primary');
        $I->seeLink('Criar Notícia');
    }

    public function testPesquisarNoticiaPorTitulo(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->fillField('NoticiaSearch[globalSearch]', 'Mosteiro');
        $I->click('button[type="submit"]');
        $I->see('Gestão de Notícias');
    }

    public function testFiltrarNoticiaPorEstado(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->selectOption('NoticiaSearch[ativo]', '1');
        $I->click('button[type="submit"]');
        $I->see('Gestão de Notícias');
    }

    public function testFiltrarNoticiaPorDestaque(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->selectOption('NoticiaSearch[destaque]', '1');
        $I->click('button[type="submit"]');
        $I->see('Gestão de Notícias');
    }

    public function testCriarNoticiaComSucesso(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/create');
        $I->see('Criar Notícia');
        
        $I->fillField('Noticia[titulo]', 'Nova Notícia Teste');
        $I->fillField('Noticia[resumo]', 'Resumo da notícia teste');
        $I->fillField('Noticia[conteudo]', 'Conteúdo completo da notícia teste');
        $I->selectOption('Noticia[local_id]', '1');
        $I->checkOption('input[name="Noticia[ativo]"][type="checkbox"]');
        
        $I->click('Guardar Alterações');
        $I->see('Gestão de Notícias');
    }

    public function testCriarNoticiaSemTitulo(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/create');
        
        $I->fillField('Noticia[resumo]', 'Resumo teste');
        $I->fillField('Noticia[conteudo]', 'Conteúdo teste');
        
        $I->click('Guardar Alterações');
        $I->see('Titulo cannot be blank');
    }

    public function testVisualizarDetalhesNoticia(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->click('.btn-info');
        $I->seeElement('.detail-view');
        $I->seeLink('Editar');
        $I->seeLink('Eliminar');
    }

    public function testEditarNoticia(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->click('.btn-warning');
        $I->see('Editar Notícia');
        
        $I->fillField('Noticia[titulo]', 'Título Editado');
        $I->click('Guardar Alterações');
        $I->see('Gestão de Notícias');
    }

    public function testMarcarNoticiaComoDestaque(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->click('.btn-warning');
        
        $I->checkOption('input[name="Noticia[destaque]"][type="checkbox"]');
        $I->click('Guardar Alterações');
        
        $I->amOnPage('/noticia/index');
        $I->see('Gestão de Notícias');
    }

    public function testDesativarNoticia(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->click('.btn-warning');
        
        $I->uncheckOption('input[name="Noticia[ativo]"][type="checkbox"]');
        $I->click('Guardar Alterações');
        
        $I->amOnPage('/noticia/index');
        $I->see('Gestão de Notícias');
    }

    public function testCancelarCriacaoNoticia(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/create');
        $I->click('Cancelar');
        $I->seeInCurrentUrl('/noticia/index');
    }

    public function testVoltarParaListagemDeView(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->click('.btn-info');
        $I->click('Voltar à Lista');
        $I->seeInCurrentUrl('/noticia/index');
    }

    public function testLimparFiltros(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->fillField('NoticiaSearch[globalSearch]', 'teste');
        $I->selectOption('NoticiaSearch[ativo]', '1');
        
        $I->click('.fa-redo');
        
        $I->seeInCurrentUrl('/noticia/index');
        $I->dontSeeInCurrentUrl('NoticiaSearch');
    }

    public function testPaginacaoNoticias(FunctionalTester $I)
    {
        $I->amOnPage('/noticia/index');
        $I->see('Gestão de Notícias');
    }
}