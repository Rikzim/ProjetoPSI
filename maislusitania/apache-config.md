# Configuração do Apache para Uploads de Imagens

Este guia explica como configurar o alias necessário para que o upload de imagens funcione corretamente no **Windows (WAMP)** ou **Linux (Apache)**.

---

## 1. Criar o Ficheiro de Configuração

### Windows (WAMP)
1. Aceder à pasta `alias` do WAMP (geralmente em `C:\wamp64\alias\`)
2. Criar um novo ficheiro `.conf` (ex: `uploads.conf`)

### Linux
1. Aceder a `/etc/apache2/conf-available/`
2. Criar o ficheiro:
   ```bash
   sudo nano uploads.conf
   ```

---

## 2. Conteúdo do Ficheiro `.conf`

### Linux
```apache
Alias /uploads /var/www/html/projetopsi/maislusitania/frontend/web/uploads

<Directory /var/www/html/projetopsi/maislusitania/frontend/web/uploads>
    AllowOverride None
    Require all granted
</Directory>
```

### Windows
```apache
Alias /uploads "C:/wamp64/www/projetopsi/maislusitania/frontend/web/uploads"

<Directory "C:/wamp64/www/projetopsi/maislusitania/frontend/web/uploads">
    AllowOverride None
    Require all granted
</Directory>
```

> **Nota:** No Windows, usar caminhos absolutos com `/` e entre aspas.

---

## 3. Reiniciar o Servidor

### Windows (WAMP)
- Reiniciar os serviços DNS no painel do WAMP

### Linux
```bash
sudo a2enconf uploads
sudo systemctl reload apache2
```

## WARNING 

- Se rebentar o servidor por causa do composer usar este comando
- composer update --ignore-platform-req=php

## Testes Unitarios e funcionais

# Testes Unitarios

- php vendor/bin/codecept generate:test unit ExampleModelsTest -c common
- php vendor/bin/codecept run unit Reservatest -c common

# Testes Funcionais 

- php vendor/bin/codecept generate:test functional ExampleFunctionalTest -c common
- php vendor/bin/codecept run functional NomeDoTesteFuncional -c common

