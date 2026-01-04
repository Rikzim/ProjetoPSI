# Documentação da API - MaisLusitânia

API RESTful para gestão de locais culturais, notícias, eventos, reservas e avaliações em Portugal.

**Base URL:** `http://localhost/projetopsi/maislusitania/backend/web/api`

---

## Índice

- [Autenticação](#autenticação)
- [Locais Culturais](#locais-culturais)
- [Eventos](#eventos)
- [Notícias](#notícias)
- [Perfil do Utilizador](#perfil-do-utilizador)
- [Favoritos](#favoritos)
- [Reservas e Bilhetes](#reservas-e-bilhetes)
- [Avaliações](#avaliações)
- [Códigos de Status HTTP](#códigos-de-status-http)

---

## Autenticação

A API utiliza **tokens de acesso** para autenticar utilizadores. O token deve ser enviado como parâmetro de query `access-token`.

### POST `/login-form`

Autentica um utilizador e retorna um token de acesso.

**Parâmetros (Body `x-www-form-urlencoded`):**

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `username` | string | Sim | Nome de utilizador |
| `password` | string | Sim | Password |

**Response (200 OK):**
```json
{
  "username": "user",
  "user_id": 8,
  "auth_key": "MhmeNGvy7wibfDGcik_kfq2RW8Tjx5bN"
}
```

### POST `/signup-form`

Regista um novo utilizador.

**Parâmetros (Body `x-www-form-urlencoded`):**

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `username` | string | Sim | Nome de utilizador único |
| `email` | string | Sim | Email válido |
| `password` | string | Sim | Password |
| `primeiro_nome` | string | Sim | Primeiro nome |
| `ultimo_nome` | string | Sim | Último nome |

**Response (201 Created):**
```json
{
  "status": "success",
  "message": "Utilizador criado com sucesso",
  "user_id": 9
}
```

---

## Locais Culturais

### GET `/local-culturals`

Lista todos os locais culturais ativos. Se autenticado, inclui `favorito` (boolean) e `favorito_id`.

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "nome": "Museu Nacional de Arte Antiga",
    "morada": "Rua das Janelas Verdes, 1249-017 Lisboa",
    "distrito": "Lisboa",
    "descricao": "O mais importante museu de arte antiga em Portugal...",
    "imagem": "http://.../imagem.jpg",
    "avaliacao_media": 4,
    "favorito": true,
    "favorito_id": 12
  }
]
```

### GET `/local-culturals/{id}`

Obtém detalhes de um local cultural. Se autenticado, inclui `favorito` e `favorito_id`.

**Response (200 OK):**
```json
{
    "id": 61,
    "nome": "Museu Nacional de Arte Antiga",
    "tipo": "Museu",
    "distrito": "Lisboa",
    "imagem": "http://.../imagem.jpg",
    "morada": "R. das Janelas Verdes, Lisboa",
    "descricao": "O Museu Nacional de Arte Antiga é o mais importante museu de arte em Portugal...",
    "contacto_telefone": "+351 213 912 800",
    "contacto_email": "mnarteantiga@mnaa.dgpc.pt",
    "website": "http://www.museudearteantiga.pt",
    "ativo": true,
    "latitude": 38.7069,
    "longitude": -9.1604,
    "horario": {
        "segunda": "10:00-18:00",
        "terca": "10:00-18:00",
        "quarta": "10:00-18:00",
        "quinta": "10:00-18:00",
        "sexta": "10:00-18:00",
        "sabado": "10:00-18:00",
        "domingo": "10:00-18:00"
    },
    "avaliacoes": [
        {
            "id": 1,
            "utilizador": "João Silva",
            "classificacao": 4.8,
            "comentario": "Espaço incrível com obras imperdíveis...",
            "data_avaliacao": "2024-02-12",
            "ativo": true
        }
    ],
    "noticias": [
        {
            "id": 1,
            "titulo": "Nova exposição de arte flamenga",
            "descricao": "Nova mostra dedicada à pintura flamenga",
            "data_publicacao": "2024-10-10 09:00",
            "imagem": "http://.../imagem.jpg"
        }
    ],
    "eventos": [
        {
            "id": 1,
            "titulo": "Concerto de Música Barroca",
            "descricao": "Apresentação especial com a Orquestra Clássica de Lisboa...",
            "data_inicio": "2024-12-05T18:00:00",
            "data_fim": "2024-12-05T20:00:00",
            "imagem": "http://.../imagem.jpg"
        }
    ],
    "tipos-bilhete": [
        {
            "id": 1,
            "nome": "Bilhete Adulto",
            "descricao": "Bilhete para maiores de 12 anos.",
            "preco": "10.00€",
            "ativo": true
        }
    ],
    "favorito": false,
    "favorito_id": null
}
```

### GET `/local-culturals/distrito/{nome}`

Lista locais culturais de um distrito.

### GET `/local-culturals/tipo-local/{nome}`

Lista locais culturais por tipo (ex: "Museu").

### GET `/local-culturals/search/{nome}`

Procura locais culturais pelo nome.

---

## Eventos

### GET `/eventos`

Lista todos os eventos ativos.

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "titulo": "Concerto de Música Barroca",
    "nome_local": "Museu Nacional de Arte Antiga",
    "descricao": "Apresentação especial com a Orquestra Clássica de Lisboa...",
    "imagem": "http://.../imagem.jpg",
    "data_inicio": "05/12/2024 18:00",
    "data_fim": "05/12/2024 20:00"
  }
]
```

### GET `/eventos/{id}`

Obtém detalhes de um evento.

**Response (200 OK):**
```json
{
    "id": 1,
    "titulo": "Concerto de Música Barroca",
    "descricao": "Apresentação especial com a Orquestra Clássica de Lisboa...",
    "data_inicio": "2024-12-05T18:00:00",
    "data_fim": "2024-12-05T20:00:00",
    "imagem": "http://.../imagem.jpg",
    "local": {
        "id": 61,
        "nome": "Museu Nacional de Arte Antiga",
        "morada": "R. das Janelas Verdes, Lisboa",
        "descricao": "O Museu Nacional de Arte Antiga é o mais importante museu de arte em Portugal...",
        "contacto_telefone": "+351 213 912 800",
        "contacto_email": "mnarteantiga@mnaa.dgpc.pt",
        "website": "http://www.museudearteantiga.pt",
        "ativo": true,
        "latitude": 38.7069,
        "longitude": -9.1604,
        "horario": {
            "segunda": "10:00-18:00",
            "terca": "10:00-18:00",
            "quarta": "10:00-18:00",
            "quinta": "10:00-18:00",
            "sexta": "10:00-18:00",
            "sabado": "10:00-18:00",
            "domingo": "10:00-18:00"
        }
    }
}
```

### GET `/eventos/tipo-local/{nome}`

Lista eventos por tipo de local cultural.

### GET `/eventos/search/{nome}`

Procura eventos pelo título.

---

## Notícias

### GET `/noticias`

Lista todas as notícias ativas.

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "nome": "Nova exposição de arte flamenga chega a Lisboa",
    "local_nome": "Museu Nacional de Arte Antiga",
    "resumo": "Nova mostra dedicada à pintura flamenga",
    "imagem": "http://.../imagem.jpg",
    "data_publicacao": "2024-10-10 09:00:00"
  }
]
```

### GET `/noticias/{id}`

Obtém detalhes de uma notícia.

**Response (200 OK):**
```json
{
    "id": 1,
    "titulo": "Nova exposição de arte flamenga",
    "descricao": "Nova mostra dedicada à pintura flamenga",
    "data_publicacao": "2024-10-10 09:00:00",
    "imagem": "http://.../imagem.jpg",
    "local": {
        "id": 61,
        "nome": "Museu Nacional de Arte Antiga",
        "morada": "R. das Janelas Verdes, Lisboa",
        "descricao": "O Museu Nacional de Arte Antiga é o mais importante museu de arte em Portugal...",
        "contacto_telefone": "+351 213 912 800",
        "contacto_email": "mnarteantiga@mnaa.dgpc.pt",
        "website": "http://www.museudearteantiga.pt",
        "ativo": true,
        "latitude": 38.7069,
        "longitude": -9.1604,
        "horario": {
            "segunda": "10:00-18:00",
            "terca": "10:00-18:00",
            "quarta": "10:00-18:00",
            "quinta": "10:00-18:00",
            "sexta": "10:00-18:00",
            "sabado": "10:00-18:00",
            "domingo": "10:00-18:00"
        }
    }
}
```

### GET `/noticias/tipo-local/{nome}`

Lista notícias por tipo de local cultural.

### GET `/noticias/search/{nome}`

Procura notícias pelo título.

---

## Perfil do Utilizador

### GET `/user-profile/me`

Obtém o perfil do utilizador autenticado.

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "primeiro_nome": "John",
    "ultimo_nome": "Doe",
    "imagem_perfil": "http://.../imagem.jpg",
    "user_id": 1,
    "username": "johndoe",
    "email": "johndoe@example.com",
    "data_adesao": "2024-01-01"
  }
]
```

### POST `/user-profile/update-profile`

Atualiza o perfil do utilizador.

**Parâmetros (Body `JSON`):**

| Campo | Tipo | Obrigatório |
|---|---|---|
| `primeiro_nome`| string| Não |
| `ultimo_nome` | string| Não |
| `username` | string| Não |

### POST `/user-profile/change-password`

Altera a password do utilizador.

**Parâmetros (Body `JSON`):**

| Campo | Tipo | Obrigatório |
|---|---|---|
| `current_password`| string| Sim |
| `new_password` | string| Sim |

### DELETE `/user-profile/delete-account`

Elimina (soft delete) a conta do utilizador.

---

## Favoritos

### GET `/favoritos`

Lista os locais culturais favoritos do utilizador.

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "utilizador_id": 1,
    "local_id": 61,
    "local_imagem": "http://.../imagem.jpg",
    "local_nome": "Museu Nacional de Arte Antiga",
    "local_distrito": "Lisboa",
    "local_rating": 4.5,
    "data_adicao": "2026-01-03 10:00:00",
    "isFavorite": true
  }
]
```

### POST `/favoritos/add/{localid}`

Adiciona um local aos favoritos.

### DELETE `/favoritos/remove/{localid}`

Remove um local dos favoritos.

### POST `/favoritos/toggle/{localid}`

Adiciona ou remove um local dos favoritos.

---

## Reservas e Bilhetes

### GET `/reservas`

Lista as reservas do utilizador.

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "local_id": 61,
    "local_nome": "Museu Nacional de Arte Antiga",
    "data_visita": "2026-02-15",
    "preco_total": "25.00",
    "estado": "confirmada",
    "data_criacao": "2026-01-04 12:30:00",
    "imagem_local": "http://.../imagem.jpg"
  }
]
```

### GET `/reservas/{id}`

Obtém os detalhes e bilhetes de uma reserva. Cada bilhete individual é retornado como um objeto.

**Response (200 OK):**
```json
[
    {
        "numero": 1,
        "codigo": "000001-001",
        "reserva_id": 1,
        "local_id": 61,
        "local_nome": "Museu Nacional de Arte Antiga",
        "data_visita": "2026-02-15",
        "tipo_bilhete_id": 1,
        "tipo_bilhete_nome": "Adulto",
        "tipo_bilhete_descricao": "Bilhete normal",
        "preco": "10.00",
        "estado": "confirmada"
    },
    {
        "numero": 2,
        "codigo": "000001-002",
        "reserva_id": 1,
        "local_id": 61,
        "local_nome": "Museu Nacional de Arte Antiga",
        "data_visita": "2026-02-15",
        "tipo_bilhete_id": 2,
        "tipo_bilhete_nome": "Criança",
        "tipo_bilhete_descricao": "Bilhete para menores de 12 anos",
        "preco": "5.00",
        "estado": "confirmada"
    }
]
```

### GET `/reservas/search/{nome}`

Procura reservas pelo nome do local cultural.

### POST `/reservas`

Cria uma nova reserva.

**Parâmetros (Body `x-www-form-urlencoded`):**

| Campo | Tipo | Descrição |
|---|---|---|
| `local_id`| integer| ID do local cultural |
| `data_visita`| date | Data da visita (YYYY-MM-DD) |
| `bilhetes` | array | Array de bilhetes. Ex: `bilhetes[TIPO_ID]=QUANTIDADE` |

---

## Avaliações

### POST `/avaliacoes/add/{localid}`

Adiciona uma avaliação a um local cultural.

**Parâmetros (Body `x-www-form-urlencoded`):**

| Campo | Tipo | Obrigatório |
|---|---|---|
| `classificacao`| float | Sim (0-5) |
| `comentario` | string| Não |

**Response (201 Created):**
```json
{
    "id": 10,
    "local_id": 61,
    "utilizador_id": 8,
    "classificacao": "4.5",
    "comentario": "Excelente museu!",
    "data_avaliacao": "2026-01-04 15:00:00",
    "ativo": 1
}
```

### DELETE `/avaliacoes/remove/{id}`

Remove uma avaliação.

---

## Códigos de Status HTTP
| Código | Descrição |
|---|---|
| `200` | **OK** |
| `201` | **Created** |
| `400` | **Bad Request** |
| `401` | **Unauthorized** |
| `403` | **Forbidden** |
| `404` | **Not Found** |
| `409` | **Conflict** |
| `500` | **Internal Server Error** |

---

**Versão:** 1.1.0  
**Última Atualização:** Janeiro 2026