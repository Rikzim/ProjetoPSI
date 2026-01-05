<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\LocalCulturalFixture;
use common\fixtures\DistritoFixture;
use common\fixtures\TipoLocalFixture;
use common\models\LocalCultural;

class LocalCulturalCest
{
    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
            'localCultural' => LocalCulturalFixture::class,
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'mariofernandes',
            'LoginForm[password]' => '12345678',
        ]);
    }

    public function _after(FunctionalTester $I)
    {
        // Limpar locais criados durante os testes
        LocalCultural::deleteAll(['nome' => 'Novo Local Teste']);
        LocalCultural::deleteAll(['nome' => 'Nome Atualizado']);
    }

    // ==================== TESTES DE VISUALIZAÇÃO INDEX ====================

    public function testAdminPodeVerListagemLocais(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->see('Gestão de Locais Culturais');
        $I->seeElement('.card-outline.card-primary');
    }

    public function testIndexMostraBotaoCriar(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->see('Criar Local Cultural');
        $I->seeElement('a[href*="local-cultural/create"]');
    }

    public function testIndexMostraFiltros(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->seeElement('#local-filter-form');
        $I->seeElement('input[name="LocalCulturalSearch[globalSearch]"]');
        $I->seeElement('select[name="LocalCulturalSearch[tipo_id]"]');
        $I->seeElement('select[name="LocalCulturalSearch[distrito_id]"]');
        $I->seeElement('select[name="LocalCulturalSearch[ativo]"]');
    }

    // ==================== TESTES DE PESQUISA E FILTROS ====================

    public function testPesquisarPorNome(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->fillField('LocalCulturalSearch[globalSearch]', 'Museu');
        $I->click('button[type="submit"]');
        $I->seeElement('.card-outline.card-primary');
    }

    public function testFiltrarPorTipo(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->selectOption('LocalCulturalSearch[tipo_id]', '1');
        $I->click('button[type="submit"]');
        $I->seeElement('.card-outline.card-primary');
    }

    public function testFiltrarPorDistrito(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->selectOption('LocalCulturalSearch[distrito_id]', '11');
        $I->click('button[type="submit"]');
        $I->seeElement('.card-outline.card-primary');
    }

    public function testFiltrarPorEstado(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index');
        $I->selectOption('LocalCulturalSearch[ativo]', '1');
        $I->click('button[type="submit"]');
        $I->seeElement('.card-outline.card-primary');
    }

    public function testLimparFiltros(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/index?tipo_id=1&distrito_id=2');
        $I->click('a[title="Limpar Filtros"]');
        $I->seeInCurrentUrl('/local-cultural/index');
        $I->seeElement('.card-outline.card-primary');
    }

    // ==================== TESTES DE VISUALIZAÇÃO VIEW ====================

    public function testAdminPodeVerDetalhesLocal(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/view?id=1');
        $I->seeElement('.card-outline.card-primary');
        $I->see('Editar');
        $I->see('Gerir Bilhetes');
        $I->see('Eliminar');
    }

    public function testViewMostraInformacoes(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/view?id=1');
        $I->seeElement('table.detail-view');
    }

    public function testViewMostraBotaoVoltar(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/view?id=1');
        $I->see('Voltar à Lista');
        $I->seeElement('a[href*="local-cultural/index"]');
    }

    // ==================== TESTES DE CRIAÇÃO ====================

    public function testAdminPodeAcessarFormularioCriacao(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/create');
        $I->see('Criar Local Cultural');
        $I->seeElement('form');
    }

    public function testFormularioCriacaoMostraCampos(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/create');
        $I->seeElement('input[name="LocalCultural[nome]"]');
        $I->seeElement('select[name="LocalCultural[tipo_id]"]');
        $I->seeElement('input[name="LocalCultural[morada]"]');
        $I->seeElement('select[name="LocalCultural[distrito_id]"]');
        $I->seeElement('textarea[name="LocalCultural[descricao]"]');
        $I->seeElement('input[name="LocalCultural[latitude]"]');
        $I->seeElement('input[name="LocalCultural[longitude]"]');
    }

    public function testCriarLocalComDadosCompletos(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/create');
        
        $I->fillField('LocalCultural[nome]', 'Novo Local Teste');
        $I->selectOption('LocalCultural[tipo_id]', '1');
        $I->fillField('LocalCultural[morada]', 'Rua Teste, 123');
        $I->selectOption('LocalCultural[distrito_id]', '1');
        $I->fillField('LocalCultural[descricao]', 'Descrição do local teste');
        $I->fillField('LocalCultural[contacto_telefone]', '912345678');
        $I->fillField('LocalCultural[contacto_email]', 'teste@exemplo.com');
        $I->fillField('LocalCultural[latitude]', '38.7223');
        $I->fillField('LocalCultural[longitude]', '-9.1393');
        
        $I->click('button[type="submit"]');
        $I->seeInCurrentUrl('local-cultural/view');
    }

    public function testCriarLocalSemCamposObrigatorios(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/create');
        $I->click('button[type="submit"]');

        $I->seeValidationError('Nome cannot be blank.');
        $I->seeValidationError('Tipo Local cannot be blank.');
        $I->seeValidationError('Morada cannot be blank.');
        $I->seeValidationError('Distrito cannot be blank.');
    }

    // ==================== TESTES DE EDIÇÃO ====================

    public function testAdminPodeAcessarFormularioEdicao(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/update?id=1');
        $I->see('Editar Local Cultural');
        $I->seeElement('form');
    }

    public function testFormularioEdicaoCarregaDados(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/update?id=2');
        $I->seeInField('LocalCultural[nome]', 'Museu Calouste Gulbenkian');
    }

    public function testEditarLocal(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/update?id=1');
        
        $I->fillField('LocalCultural[nome]', 'Nome Atualizado');
        $I->fillField('LocalCultural[descricao]', 'Descrição atualizada');
        
        $I->click('button[type="submit"]');
        $I->seeInCurrentUrl('local-cultural/view');
    }

    // ==================== TESTES DE ELIMINAÇÃO ====================

    public function testViewMostraBotaoEliminar(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/view?id=1');
        $I->see('Eliminar');
        $I->seeElement('.btn-danger');
    }

    // ==================== TESTES DE HORÁRIOS ====================

    public function testFormularioCriacaoMostraCamposHorario(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/create');
        $I->seeElement('input[name="Horario[segunda]"]');
        $I->seeElement('input[name="Horario[terca]"]');
        $I->seeElement('input[name="Horario[quarta]"]');
        $I->seeElement('input[name="Horario[quinta]"]');
        $I->seeElement('input[name="Horario[sexta]"]');
        $I->seeElement('input[name="Horario[sabado]"]');
        $I->seeElement('input[name="Horario[domingo]"]');
    }

    public function testFormularioEdicaoMostraCamposHorario(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/update?id=1');
        $I->seeElement('input[name="Horario[segunda]"]');
        $I->seeElement('input[name="Horario[terca]"]');
        $I->seeElement('input[name="Horario[quarta]"]');
        $I->seeElement('input[name="Horario[quinta]"]');
        $I->seeElement('input[name="Horario[sexta]"]');
        $I->seeElement('input[name="Horario[sabado]"]');
        $I->seeElement('input[name="Horario[domingo]"]');
    }

    // ==================== TESTES DE UPLOAD DE IMAGEM ====================

    public function testFormularioCriacaoMostraCampoImagem(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/create');
        $I->seeElement('input[name="UploadForm[imageFile]"]');
    }

    public function testFormularioEdicaoMostraCampoImagem(FunctionalTester $I)
    {
        $I->amOnPage('/local-cultural/update?id=1');
        $I->seeElement('input[name="UploadForm[imageFile]"]');
    }
}