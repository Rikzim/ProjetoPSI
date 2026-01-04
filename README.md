<p align="center">
  <img src="maislusitania/frontend/web/images/logo/logo.svg" alt="Mais Lusitânia Logo" width="300" style="background-color:white; padding:10px;">
</p>

# Mais Lusitânia 🇵🇹

> Uma plataforma web para explorar e descobrir locais e eventos culturais em Portugal.

## 📋 Sobre o Projeto

**Mais Lusitânia** é uma aplicação web desenvolvida com o framework Yii2, que serve como um guia agregador de informações sobre o património cultural português. A plataforma permite aos utilizadores descobrir locais culturais, consultar eventos, ler notícias e efetuar reservas, promovendo o turismo e a cultura em Portugal.

O projeto está estruturado com uma arquitetura que inclui um frontend para os utilizadores, um backend para administração e uma API RESTful para servir dados a aplicações cliente (como uma aplicação móvel).

### 🎯 Componentes Principais

1.  **🌐 Aplicação Web (Frontend)** - Interface pública para os utilizadores explorarem locais, eventos e notícias.
2.  **⚙️ Painel de Administração (Backend)** - Área de gestão de conteúdos para os administradores da plataforma.
3.  **🔧 API REST** - Backend robusto com arquitetura RESTful para comunicação com aplicações cliente.

## ✨ Funcionalidades

### Frontend
- Pesquisa e visualização de locais culturais e eventos.
- Visualização de notícias culturais.
- Registo e autenticação de utilizadores.
- Área de perfil de utilizador.
- Sistema de favoritos para locais e eventos.
- Realização de reservas para eventos.
- Sistema de avaliação de locais e eventos.
- Visualização de locais culturais num mapa interativo.

### Backend
- Gestão completa de utilizadores, locais culturais, tipos de locais, distritos.
- Gestão de eventos, bilhetes e reservas.
- Gestão de notícias.
- Moderação de avaliações.

### API
- Endpoints para todas as funcionalidades principais, permitindo a integração com outras aplicações.
- Autenticação e registo de utilizadores.
- Acesso a dados de locais, eventos, notícias, reservas, favoritos, etc.

## 🛠️ Stack Tecnológica

- **Backend & Frontend:** PHP 7+, Yii2 Framework
- **Base de Dados:** MySQL / MariaDB
- **Servidor Web:** Apache / Nginx
- **Gestor de Dependências:** Composer

## 🚀 Instalação e Configuração

Siga os passos abaixo para configurar o ambiente de desenvolvimento local.

### Pré-requisitos

- PHP >= 7.4
- Composer
- Git
- Servidor Web (ex: WAMP, XAMPP, Laragon para Windows, ou Docker)
- Base de dados MySQL ou MariaDB

### Configuração do Ambiente

1.  **Clonar o repositório:**
    ```bash
    git clone <URL_DO_REPOSITORIO>
    cd maislusitania
    ```

2.  **Instalar dependências PHP com Composer:**
    Navegue até à pasta `maislusitania` e execute:
    ```bash
    composer install
    ```

3.  **Inicializar o ambiente Yii2:**
    Execute o ficheiro `init` na raiz da pasta `maislusitania`. Escolha `dev` (desenvolvimento) quando solicitado.
    ```bash
    php init
    ```

4.  **Configurar a Base de Dados:**
    - Crie uma nova base de dados (ex: `maislusitania_db`).
    - Configure a ligação à base de dados no ficheiro `maislusitania/common/config/main-local.php`:
      ```php
      'components' => [
          'db' => [
              'class' => 'yii\db\Connection',
              'dsn' => 'mysql:host=localhost;dbname=maislusitania_db',
              'username' => 'root',
              'password' => '',
              'charset' => 'utf8',
          ],
          // ...
      ],
      ```

5.  **Executar as Migrações da Base de Dados:**
    As migrações criam a estrutura de tabelas necessárias. Na raiz da pasta `maislusitania`, execute:
    ```bash
    php yii migrate
    ```

6.  **Inicializar as Funções e Permissões (RBAC):**
    Este passo é crucial para que o sistema de permissões funcione. Execute o seguinte comando:
    ```bash
    php yii rbac/init
    ```

7.  **Configurar o Servidor Web (Exemplo para Apache):**
    Configure os seus Virtual Hosts para apontarem para as pastas `frontend/web` and `backend/web`.

    **Frontend:**
    - `DocumentRoot "c:/wamp64/www/projetopsi/maislusitania/frontend/web"`
    - `ServerName maislusitania.local`

    **Backend:**
    - `DocumentRoot "c:/wamp64/www/projetopsi/maislusitania/backend/web"`
    - `ServerName admin.maislusitania.local`

    Não se esqueça de adicionar estes domínios ao seu ficheiro `hosts`.

## 📖 Como Usar

-   **Frontend:** Aceda a `http://maislusitania.local` (ou o URL que configurou) no seu browser.
-   **Backend:** Aceda a `http://admin.maislusitania.local` (ou o URL que configurou) para o painel de administração.

### API Endpoints

A API está disponível através do backend. Exemplo de alguns endpoints:

```
GET    /api/locais-culturais       # Listar locais culturais
GET    /api/locais-culturais/{id}  # Obter detalhes de um local cultural
GET    /api/eventos                # Listar eventos
POST   /api/signup                 # Registar um novo utilizador
POST   /api/login                  # Autenticar um utilizador
```

## 📚 Documentação

Para mais detalhes sobre os endpoints disponíveis, consulte a [Documentação da API](./api.md).

## 🧪 Testes

O projeto utiliza Codeception para os testes. Para executar os testes, configure as bases de dados de teste nos ficheiros de configuração (`common/config/test-local.php`, etc.) e execute os testes a partir da raiz do projeto `maislusitania`:

```bash
# Executar testes unitários do common
vendor/bin/codecept run unit -c common

# Executar testes funcionais do frontend
vendor/bin/codecept run functional -c frontend
```


