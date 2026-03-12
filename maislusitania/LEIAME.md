# +Lusitânia — Guia de Instalação com Docker

Plataforma de turismo cultural lusitano desenvolvida em **Yii2 Advanced Template**, containerizada com **Docker**.

---

## Índice

- [Pré-requisitos](#pré-requisitos)
- [Estrutura dos Serviços](#estrutura-dos-serviços)
- [Configuração Inicial](#configuração-inicial)
- [Passo Crítico — Base de Dados](#passo-crítico--base-de-dados)
- [Iniciar o Projeto](#iniciar-o-projeto)
- [Aceder à Aplicação](#aceder-à-aplicação)
- [Comandos Disponíveis](#comandos-disponíveis)
- [Estrutura de Pastas](#estrutura-de-pastas)
- [Notas Importantes](#notas-importantes)

---

## Pré-requisitos

Antes de começar, certifica-te que tens o seguinte instalado na tua máquina:

| Ferramenta | Versão mínima | Download |
|---|---|---|
| Docker Desktop | 24+ | https://www.docker.com/products/docker-desktop |
| Docker Compose | incluído no Docker Desktop | — |
| Make | qualquer | já incluído no Linux/macOS · no Windows usa [Git Bash](https://git-scm.com/) ou [WSL2](https://learn.microsoft.com/pt-pt/windows/wsl/) |
| Git | qualquer | https://git-scm.com/ |

> **Windows:** Todos os comandos `make` devem ser executados dentro do **Git Bash** ou **WSL2**, nunca no CMD ou PowerShell.

---

## Estrutura dos Serviços

O projeto corre com **5 contentores Docker**:

| Contentor | Descrição | Porta local |
|---|---|---|
| `maislusitania-app` | Frontoffice (aplicação pública) | `8080` |
| `maislusitania-admin` | Backoffice (painel de administração) | `8081` |
| `maislusitania-api` | API REST (usada pela aplicação móvel/frontend) | `8082` |
| `maislusitania-db` | Base de dados MySQL 5.7 | `3306` |
| `maislusitania-phpmyadmin` | Interface gráfica para a base de dados | `8083` |

Todos os contentores comunicam entre si numa rede interna Docker chamada `maislusitania-network`.

---

## Configuração Inicial

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio>
cd maislusitania
```

### 2. Criar a pasta de cache do Composer (apenas uma vez)

O Docker reutiliza a cache local do Composer para acelerar instalações futuras:

```bash
mkdir -p ~/.composer-docker/cache
```

---

## Passo Crítico — Base de Dados

> ⚠️ **Este passo é obrigatório.** Sem ele a aplicação não consegue ligar à base de dados.

Depois de correr o `make init` (incluído no `make setup`), o ficheiro  
`common/config/main-local.php` é criado automaticamente pelo Yii2.  
Por defeito, esse ficheiro aponta para `localhost`, o que **não funciona dentro do Docker**.

Tens de abrir o ficheiro e alterar o host:

**Ficheiro:** `common/config/main-local.php`

Encontra esta linha:
```php
'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
```

Substitui por:
```php
'dsn' => 'mysql:host=maislusitania-db;dbname=yii2advanced',
```

Dentro do Docker, os contentores comunicam pelo **nome do serviço**, não por `localhost`.  
O nome do serviço da base de dados é `maislusitania-db`.

> 💡 Se correres `make setup` pela primeira vez, o setup vai pausar nas migrações porque este ficheiro ainda não existe. Segue a ordem: `make build` → `make up` → `make init` → **edita o ficheiro** → `make migrate`.

---

## Iniciar o Projeto

### Primeira vez (setup completo)

```bash
make setup
```

Este comando executa automaticamente e por ordem:

1. `make build` — constrói as imagens Docker
2. `make up` — inicia todos os contentores em segundo plano
3. `make wait-db` — aguarda que o MySQL esteja pronto
4. `make composer` — instala as dependências PHP via Composer
5. `make init` — inicializa o ambiente Yii2 (Development)
6. `make migrate` — corre todas as migrações da base de dados

> ⚠️ **Lembra-te de editar o `common/config/main-local.php`** conforme descrito acima antes de correr `make migrate`, caso o faças separadamente.

No final do setup bem-sucedido verás:

```
✔ Setup complete!
  Frontoffice  →  http://localhost:8080
  Backoffice   →  http://localhost:8081
  API          →  http://localhost:8082
  phpMyAdmin   →  http://localhost:8083
```

---

### Utilizações seguintes

Depois do setup inicial, para iniciar o projeto basta:

```bash
make up
```

Para parar tudo:

```bash
make down
```

---

## Aceder à Aplicação

| Serviço | URL | Descrição |
|---|---|---|
| Frontoffice | http://localhost:8080 | Aplicação pública para utilizadores |
| Backoffice | http://localhost:8081 | Painel de administração |
| API REST | http://localhost:8082/api/ | Endpoints REST da aplicação |
| phpMyAdmin | http://localhost:8083 | Gestão visual da base de dados |

### Credenciais da Base de Dados

| Campo | Valor |
|---|---|
| Host (dentro do Docker) | `maislusitania-db` |
| Host (do teu computador) | `localhost:3306` |
| Base de dados | `yii2advanced` |
| Utilizador | `yii2advanced` |
| Password | `secret` |
| Root password | `verysecret` |

---

## Comandos Disponíveis

Corre `make help` para ver todos os comandos disponíveis com descrição.

### Docker

| Comando | Descrição |
|---|---|
| `make build` | Constrói todas as imagens Docker |
| `make up` | Inicia todos os contentores em segundo plano |
| `make down` | Para e remove os contentores (os volumes ficam) |
| `make restart` | Reinicia todos os contentores |
| `make ps` | Mostra o estado de todos os contentores |

### Logs

| Comando | Descrição |
|---|---|
| `make logs` | Segue os logs de todos os contentores |
| `make logs-app` | Logs do frontoffice |
| `make logs-admin` | Logs do backoffice |
| `make logs-api` | Logs da API |
| `make logs-db` | Logs do MySQL |

### Aplicação

| Comando | Descrição |
|---|---|
| `make composer` | Instala dependências Composer dentro do contentor |
| `make init` | Inicializa o ambiente Yii2 (Development) |
| `make migrate` | Corre as migrações pendentes da base de dados |
| `make wait-db` | Aguarda o MySQL estar pronto para aceitar ligações |
| `make setup` | Setup completo de primeira execução |

### Acesso aos Contentores

| Comando | Descrição |
|---|---|
| `make shell-app` | Abre bash no contentor do frontoffice |
| `make shell-admin` | Abre bash no contentor do backoffice |
| `make shell-api` | Abre bash no contentor da API |
| `make shell-db` | Abre bash no contentor do MySQL |

### Limpeza

| Comando | Descrição |
|---|---|
| `make clean` | Para contentores, remove volumes e imagens locais construídas |

> ⚠️ `make clean` apaga a base de dados. Usa apenas quando queres um reset completo.

---

## Estrutura de Pastas

```
maislusitania/
├── backend/                   # Backoffice + API REST
│   ├── docker/
│   │   └── uploads.conf       # Alias Apache /uploads para o backoffice
│   ├── Dockerfile
│   └── ...
├── frontend/                  # Frontoffice público
│   ├── Dockerfile
│   └── web/
│       └── uploads/           # Pasta onde as imagens são guardadas
├── common/
│   ├── config/
│   │   ├── main.php
│   │   ├── main-local.php     # ⚠️ Criado pelo init — editar o host da DB
│   │   └── bootstrap.php      # Define @uploadPath
│   └── models/
├── console/
│   └── migrations/            # Migrações da base de dados
├── environments/              # Templates de configuração por ambiente
├── docker-compose.yml         # Orquestração dos 5 contentores
├── Makefile                   # Comandos simplificados
└── composer.json
```

---

## Notas Importantes

### Uploads de Imagens

As imagens carregadas através do backoffice são guardadas em:

```
frontend/web/uploads/
```

O alias `@uploadPath` em PHP aponta para esta pasta em ambos os contentores.  
No contentor do backoffice (`maislusitania-admin` e `maislusitania-api`), o Apache tem  
um alias configurado automaticamente via `backend/docker/uploads.conf`:

```apache
Alias /uploads /app/frontend/web/uploads
```

Isto garante que os URLs do tipo `/uploads/imagem.jpg` funcionam corretamente  
tanto no frontoffice como no backoffice, sem alterar qualquer lógica da aplicação.

### Persistência da Base de Dados

Os dados do MySQL são guardados num volume Docker chamado `maislusitania-db-data`.  
Isto significa que os dados **sobrevivem** a um `make down` e `make up`.  
Só são apagados se correres `make clean`.

### Composer Cache

A pasta `~/.composer-docker/cache` no teu computador é partilhada com os contentores  
para evitar re-descarregar pacotes em cada rebuild. É criada automaticamente no passo 2.

### Problemas Comuns

**Erro de ligação à base de dados:**  
Verifica se o `common/config/main-local.php` tem `host=maislusitania-db` e não `host=localhost`.

**Porta já em uso:**  
Se as portas 8080, 8081, 8082, 8083 ou 3306 já estiverem a ser usadas no teu sistema,  
altera os valores no lado esquerdo do `docker-compose.yml` (ex: `"9080:80"`).

**Permissões na pasta uploads:**  
Se os uploads falharem por questões de permissões, corre dentro do contentor:
```bash
make shell-admin
chmod -R 777 /app/frontend/web/uploads
```

**Composer falha com erro de versão PHP:**  
Corre dentro do contentor:
```bash
make shell-admin
composer update --ignore-platform-req=php
```
