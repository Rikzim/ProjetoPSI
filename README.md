<div align="center">
  <img src="maislusitania/frontend/web/images/logo/logo.svg" alt="Mais Lusitânia Logo" width="300" style="background-color:white; padding:10px; vertical-align: middle;">
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg" alt="Docker Logo" width="100" style="vertical-align: middle;">
</div>

# +Lusitânia — Guia de Instalação com Docker

Plataforma de turismo cultural lusitano desenvolvida em **Yii2 Advanced Template**, containerizada com **Docker**.

---

## Índice

- [Pré-requisitos](#pré-requisitos)
- [Estrutura dos Serviços](#estrutura-dos-serviços)
- [Iniciar o Projeto](#iniciar-o-projeto)
- [Aceder à Aplicação](#aceder-à-aplicação)
- [Credenciais](#credenciais)
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

## Iniciar o Projeto

### Primeira vez (setup completo)

```bash
git clone <url-do-repositorio>
cd maislusitania

# O Docker reutiliza a cache local do Composer, por isso criamos a pasta primeiro
mkdir -p ~/.composer-docker/cache

# Comando mágico que faz tudo
sudo make setup
```

O comando `make setup` executa automaticamente tudo o que é necessário (build das imagens, iniciar contentores, instalar dependências, preparar a base de dados e aplicar permissões). Não é preciso fazer mais nada!

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

Depois do setup inicial, para iniciar o projeto no dia a dia basta:

```bash
sudo make up
```

Para parar tudo:

```bash
sudo make down
```

---

## Aceder à Aplicação

| Serviço | URL | Descrição |
|---|---|---|
| Frontoffice | http://localhost:8080 | Aplicação pública para utilizadores |
| Backoffice | http://localhost:8081 | Painel de administração |
| API REST | http://localhost:8082/api/ | Endpoints REST da aplicação |
| phpMyAdmin | http://localhost:8083 | Gestão visual da base de dados |

---

## Credenciais

### Aplicação

| Perfil | Username | Password |
|---|---|---|
| **Administrador** | `admin` | `12345678` |
| **Gestor** | `gestor` | `12345678` |
| **Utilizador** | `user` | `12345678` |

### Base de Dados (phpMyAdmin / Aplicação)

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
| `make permissions`| Aplica as permissões de escrita corretas nas pastas (`uploads`, `assets`, etc) |
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
│   │   ├── main-local.php     # Criado pelo init (já com o host correto via Makefile)
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

A base de dados é populada automaticamente na primeira vez que o contentor é iniciado, através do ficheiro `yii2advanced.sql`.

### Composer Cache

A pasta `~/.composer-docker/cache` no teu computador é partilhada com os contentores  
para evitar re-descarregar pacotes em cada rebuild. É criada automaticamente no passo 2.

### Problemas Comuns

**Porta já em uso:**  
Se as portas 8080, 8081, 8082, 8083 ou 3306 já estiverem a ser usadas no teu sistema,  
altera os valores no lado esquerdo do `docker-compose.yml` (ex: `"9080:80"`).

**Composer falha com erro de versão PHP:**  
Corre dentro do contentor:
```bash
make shell-admin
composer update --ignore-platform-req=php
```