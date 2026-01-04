<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\NoticiaFixture;
use common\fixtures\LocalCulturalFixture;

class NoticiasTest extends \Codeception\Test\Unit
{
    protected FunctionalTester $tester;

    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
            'noticia' => NoticiaFixture::class,
            'local' => LocalCulturalFixture::class,
        ];
    }

    protected function _before()
    {
        // Login como admin antes de cada teste
        $this->tester->amOnPage('/site/login');
        $this->tester->submitForm('#login-form', [
            'LoginForm[username]' => 'mariofernandes',
            'LoginForm[password]' => '12345678',
        ]);
    }

    public function testVisualizarListagemNoticias()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->see('Gestão de Notícias');
        $this->tester->seeElement('.card-outline.card-primary');
        $this->tester->seeLink('Criar Notícia');
    }

    public function testPesquisarNoticiaPorTitulo()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->fillField('NoticiaSearch[globalSearch]', 'Mosteiro');
        $this->tester->click('button[type="submit"]');
        // Verificar que a página carregou (mesmo que não encontre resultados)
        $this->tester->see('Gestão de Notícias');
    }

    public function testFiltrarNoticiaPorEstado()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->selectOption('NoticiaSearch[ativo]', '1');
        $this->tester->click('button[type="submit"]');
        $this->tester->see('Gestão de Notícias');
    }

    public function testFiltrarNoticiaPorDestaque()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->selectOption('NoticiaSearch[destaque]', '1');
        $this->tester->click('button[type="submit"]');
        $this->tester->see('Gestão de Notícias');
    }

    public function testCriarNoticiaComSucesso()
    {
        $this->tester->amOnPage('/noticia/create');
        $this->tester->see('Criar Notícia');
        
        $this->tester->fillField('Noticia[titulo]', 'Nova Notícia Teste');
        $this->tester->fillField('Noticia[resumo]', 'Resumo da notícia teste');
        $this->tester->fillField('Noticia[conteudo]', 'Conteúdo completo da notícia teste');
        $this->tester->selectOption('Noticia[local_id]', '1'); // String em vez de int
        $this->tester->checkOption('input[name="Noticia[ativo]"][type="checkbox"]');
        
        $this->tester->click('Guardar Alterações');
        $this->tester->see('Gestão de Notícias');
    }

    public function testCriarNoticiaSemTitulo()
    {
        $this->tester->amOnPage('/noticia/create');
        
        $this->tester->fillField('Noticia[resumo]', 'Resumo teste');
        $this->tester->fillField('Noticia[conteudo]', 'Conteúdo teste');
        
        $this->tester->click('Guardar Alterações');
        $this->tester->see('Titulo cannot be blank');
    }

    public function testVisualizarDetalhesNoticia()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->click('.btn-info'); // Botão Ver
        $this->tester->seeElement('.detail-view');
        $this->tester->seeLink('Editar');
        $this->tester->seeLink('Eliminar');
    }

    public function testEditarNoticia()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->click('.btn-warning'); // Botão Editar
        $this->tester->see('Editar Notícia');
        
        $this->tester->fillField('Noticia[titulo]', 'Título Editado');
        $this->tester->click('Guardar Alterações');
        $this->tester->see('Gestão de Notícias');
    }

    public function testMarcarNoticiaComoDestaque()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->click('.btn-warning'); // Editar primeira notícia
        
        // Usar o checkbox correto (não o hidden)
        $this->tester->checkOption('input[name="Noticia[destaque]"][type="checkbox"]');
        $this->tester->click('Guardar Alterações');
        
        $this->tester->amOnPage('/noticia/index');
        $this->tester->see('Gestão de Notícias');
    }

    public function testDesativarNoticia()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->click('.btn-warning'); // Editar
        
        // Usar o checkbox correto (não o hidden)
        $this->tester->uncheckOption('input[name="Noticia[ativo]"][type="checkbox"]');
        $this->tester->click('Guardar Alterações');
        
        $this->tester->amOnPage('/noticia/index');
        $this->tester->see('Gestão de Notícias');
    }

    /*public function testEliminarNoticia() // Infelizmente este metodo nao funciona pois o delete no index usa pjax com javascript, o que o codeception nao suporta.
    {
        $this->tester->amOnPage('/noticia/index');
        // Verificar que existe pelo menos uma notícia
        $this->tester->seeElement('.card');
        $this->tester->click('.btn-danger'); // Botão Eliminar
        $this->tester->see('Gestão de Notícias');
    }*/

    public function testCancelarCriacaoNoticia()
    {
        $this->tester->amOnPage('/noticia/create');
        $this->tester->click('Cancelar');
        $this->tester->seeInCurrentUrl('/noticia/index');
    }

    public function testVoltarParaListagemDeView()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->click('.btn-info'); // Ver detalhes
        $this->tester->click('Voltar à Lista');
        $this->tester->seeInCurrentUrl('/noticia/index');
    }

    public function testLimparFiltros()
    {
        $this->tester->amOnPage('/noticia/index');
        $this->tester->fillField('NoticiaSearch[globalSearch]', 'teste');
        $this->tester->selectOption('NoticiaSearch[ativo]', '1');
        
        $this->tester->click('.fa-redo'); // Botão Limpar
        
        // Após limpar, redireciona para a página sem filtros
        $this->tester->seeInCurrentUrl('/noticia/index');
        $this->tester->dontSeeInCurrentUrl('NoticiaSearch');
    }

    public function testPaginacaoNoticias()
    {
        $this->tester->amOnPage('/noticia/index');
        // Apenas verificar que a página carrega
        $this->tester->see('Gestão de Notícias');
    }
}