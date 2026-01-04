<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\LocalCulturalFixture;

class FavoritosTest extends \Codeception\Test\Unit
{
    protected FunctionalTester $tester;

    public function _fixtures()
    {
        return [
            'user' => UserFixture::class,
            'local' => LocalCulturalFixture::class,
        ];
    }

    protected function _before()
    {
        // Login antes de cada teste
        $this->tester->amOnPage('/site/login');
        $this->tester->submitForm('#login-form', [
            'LoginForm[username]' => 'joaomatias',
            'LoginForm[password]' => '12345678',
        ]);
    }

    public function testAdicionarFavorito()
    {
        // Ir para a página de locais culturais
        $this->tester->amOnPage('/local-cultural/index');
        $this->tester->see('Explore o Património de Portugal');
        
        // Clicar no botão de favoritar (primeiro local da lista)
        $this->tester->click('.btn-favorite');
        
        // Verificar que o botão mudou para favorited
        $this->tester->seeElement('.btn-favorite.favorited');
    }

    public function testRemoverFavorito()
    {
        // Adicionar favorito primeiro
        $this->tester->amOnPage('/local-cultural/index');
        $this->tester->click('.btn-favorite');
        $this->tester->seeElement('.btn-favorite.favorited');
        
        // Remover o favorito
        $this->tester->click('.btn-favorite.favorited');
        
        // Verificar que não está mais favoritado
        $this->tester->SeeElement('.btn-favorite');
    }

    public function testVisualizarPaginaFavoritos()
    {
        // Adicionar um favorito
        $this->tester->amOnPage('/local-cultural/index');
        $this->tester->click('.btn-favorite');
        
        // Ir para a página de favoritos
        $this->tester->amOnPage('/favorito/index');
        $this->tester->see('Meus Favoritos');
        
        // Verificar que existe pelo menos um local na grid
        $this->tester->seeElement('.favorites-grid');
        $this->tester->seeElement('.favorite-card');
    }

    public function testRemoverFavoritoDaPaginaFavoritos()
    {
        // Adicionar um favorito
        $this->tester->amOnPage('/local-cultural/index');
        $this->tester->click('.btn-favorite');
        
        // Ir para a página de favoritos
        $this->tester->amOnPage('/favorito/index');
        $this->tester->seeElement('.favorite-card');
        
        // Remover o favorito
        $this->tester->click('.btn-unfavorite');
        
        // Recarregar a página
        $this->tester->amOnPage('/favorito/index');
        
        // Verificar que mostra mensagem de lista vazia
        $this->tester->see('Ainda sem favoritos');
    }

    public function testPaginaFavoritosVazia()
    {
        // Ir para a página de favoritos sem adicionar nenhum
        $this->tester->amOnPage('/favorito/index');
        
        // Verificar que mostra o empty state
        $this->tester->see('Ainda sem favoritos');
        $this->tester->see('Explore os nossos locais culturais');
        $this->tester->seeLink('Explorar Locais');
    }

    public function testFavoritoSemLogin()
    {
        // Fazer logout através do menu dropdown
        $this->tester->amOnPage('/');
        $this->tester->click('.user-avatar'); // Abrir dropdown
        $this->tester->click('.user-dropdown-item.logout-item'); // Clicar no botão de logout
        $this->tester->seeElement('.btn-login'); // Verificar que está na página de login
        
        // Tentar acessar favoritos
        $this->tester->amOnPage('/favorito/index');
        
        // Verificar redirecionamento para login
        $this->tester->seeInCurrentUrl('/site/login');
    }

    public function testContadorDeFavoritos()
    {
        // Adicionar múltiplos favoritos
        $this->tester->amOnPage('/local-cultural/index');
        
        // Favoritar primeiro local
        $this->tester->click('.local-card:nth-child(1) .btn-favorite');
        
        // Favoritar segundo local
        $this->tester->click('.local-card:nth-child(2) .btn-favorite');
        
        // Ir para a página de favoritos
        $this->tester->amOnPage('/favorito/index');
        
        // Verificar contador
        $this->tester->see('2 Locais Favoritos');
    }
}