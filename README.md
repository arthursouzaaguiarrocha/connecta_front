# Connecta — Agenda inteligente de contatos

Aplicação PHP MVC + SQLite com interface moderna e responsiva.

## Módulos

- **Landing page** alinhada ao design
- **Login / Cadastro** (com botões de login social mockados)
- **Contatos** — cards com canais (WhatsApp, e-mail, Teams, Telegram)
- **Agenda** — calendário mensal + eventos do dia + banner Connecta Sync
- **Tarefas** — prioridades (alta/média/baixa) e status
- **Grupos** — tags, contadores e mensagens
- **Configurações** — perfil, senha, notificações, privacidade e tema

## Requisitos

- PHP 8.1+ com `pdo_sqlite` e `mbstring`
- Apache (XAMPP recomendado) ou servidor embutido do PHP

## Instalação

1. Coloque a pasta `backend` (ou o projeto inteiro) em `htdocs` / `www`.
2. Ajuste `BASE_URL` em `config/config.php` (ex.: `/connecta/public`).
3. Garanta permissão de escrita em `storage/`.
4. Acesse `http://localhost/connecta/public/`.

O banco SQLite e as tabelas são criados automaticamente na primeira execução.

### Servidor embutido (desenvolvimento)

```bash
cd backend/public
php -S localhost:8080
```

Acesse `http://localhost:8080` (ajuste `BASE_URL` para `''` ou `/` se necessário).

## Estrutura

```
backend/
├── app/
│   ├── controllers/
│   ├── core/
│   ├── models/
│   ├── views/
│   └── helpers.php
├── config/
├── database/
├── public/
│   ├── assets/
│   └── index.php
└── storage/          # connecta.sqlite (gerado)
```

## Segurança

- Senhas com `password_hash` / `password_verify`
- Token CSRF em formulários de alteração
- Consultas preparadas PDO
- Isolamento de dados por `user_id`

## Próximos passos sugeridos

- Integração real Google Calendar / Outlook
- Login social (OAuth)
- Upload de avatar
- Notificações push
- API REST para app mobile
