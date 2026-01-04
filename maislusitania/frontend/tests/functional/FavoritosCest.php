<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\LocalCulturalFixture;

class FavoritosCest
{
    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
            'local' => LocalCulturalFixture::class,
        ];
    }

    protected function login(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'joaomatias',
            'LoginForm[password]' => '12345678',
        ]);
    }

    public function testAdicionarFavorito(FunctionalTester $I)
    {
        $this->login($I);
        
        // Ir para a página de locais culturais
        $I->amOnPage('/local-cultural/index');
        $I->see('Explore o Património de Portugal');
        
        // Clicar no botão de favoritar (primeiro local da lista)
        $I->click('.btn-favorite');
        
        // Verificar que o botão mudou para favorited
        $I->seeElement('.btn-favorite.favorited');
    }

    public function testRemoverFavorito(FunctionalTester $I)
    {
        $this->login($I);
        
        // Adicionar favorito primeiro
        $I->amOnPage('/local-cultural/index');
        $I->click('.btn-favorite');
        $I->seeElement('.btn-favorite.favorited');
        
        // Remover o favorito
        $I->click('.btn-favorite.favorited');
        
        // Verificar que não está mais favoritado
        $I->seeElement('.btn-favorite');
    }

    public function testVisualizarPaginaFavoritos(FunctionalTester $I)
    {
        $this->login($I);
        
        // Adicionar um favorito
        $I->amOnPage('/local-cultural/index');
        $I->click('.btn-favorite');
        
        // Ir para a página de favoritos
        $I->amOnPage('/favorito/index');
        $I->see('Meus Favoritos');
        
        // Verificar que existe pelo menos um local na grid
        $I->seeElement('.favorites-grid');
        $I->seeElement('.favorite-card');
    }

    public function testRemoverFavoritoDaPaginaFavoritos(FunctionalTester $I)
    {
        $this->login($I);
        
        // Adicionar um favorito
        $I->amOnPage('/local-cultural/index');
        $I->click('.btn-favorite');
        
        // Ir para a página de favoritos
        $I->amOnPage('/favorito/index');
        $I->seeElement('.favorite-card');
        
        // Remover o favorito
        $I->click('.btn-unfavorite');
        
        // Recarregar a página
        $I->amOnPage('/favorito/index');
        
        // Verificar que mostra mensagem de lista vazia
        $I->see('Ainda sem favoritos');
    }

    public function testPaginaFavoritosVazia(FunctionalTester $I)
    {
        $this->login($I);
        
        // Ir para a página de favoritos sem adicionar nenhum
        $I->amOnPage('/favorito/index');
        
        // Verificar que mostra o empty state
        $I->see('Ainda sem favoritos');
        $I->see('Explore os nossos locais culturais');
        $I->seeLink('Explorar Locais');
    }

    public function testFavoritoSemLogin(FunctionalTester $I)
    {
        // Tentar acessar favoritos sem login
        $I->amOnPage('/favorito/index');
        
        // Verificar redirecionamento para login
        $I->seeInCurrentUrl('/site/login');
    }

    public function testContadorDeFavoritos(FunctionalTester $I)
    {
        $this->login($I);
        
        // Adicionar múltiplos favoritos
        $I->amOnPage('/local-cultural/index');
        
        // Favoritar primeiro local
        $I->click('.local-card:nth-child(1) .btn-favorite');
        
        // Favoritar segundo local
        $I->click('.local-card:nth-child(2) .btn-favorite');
        
        // Ir para a página de favoritos
        $I->amOnPage('/favorito/index');
        
        // Verificar contador
        $I->see('2 Locais Favoritos');
    }
}