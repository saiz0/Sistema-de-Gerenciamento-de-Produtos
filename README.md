# Sistema de Gerenciamento de Produtos

Backend para cadastro e manutenção de empresas fornecedoras e seus respectivos produtos.

A aplicação utiliza o Laravel somente como API. O frontend é mantido fora deste projeto e não existem dependências de Node.js, NPM ou Vite.

## Tecnologias

- PHP 8.4;
- Laravel 13;
- PostgreSQL 17;
- Docker e Docker Compose;
- Composer 2;
- PHPUnit 12.

## Serviços Docker

| Serviço | Finalidade |
|---|---|
| `backend` | Aplicação Laravel disponível na porta `8000` |
| `postgres` | Banco de desenvolvimento com dados persistentes |
| `backend-test` | Execução isolada da suíte de testes |
| `postgres-test` | Banco temporário usado exclusivamente pelos testes |

O banco de desenvolvimento utiliza um volume persistente. O banco de testes utiliza `tmpfs` e é descartado ao encerrar o container.

## Execução

Crie o arquivo de configuração do Docker a partir do exemplo:

```bash
cp .env.example .env
```

Revise as portas e credenciais locais no `.env`. Depois, com Docker instalado, execute na raiz do projeto:

```bash
docker compose up -d --build
```

A aplicação ficará disponível em:

```text
http://localhost:8000
```

O health check pode ser consultado em:

```text
http://localhost:8000/up
```

As rotas da aplicação são registradas em `backend/routes/api.php` e recebem automaticamente o prefixo `/api`. Requisições para rotas inexistentes retornam erro em JSON.

### Migrations

Execute as migrations no banco de desenvolvimento:

```bash
docker compose exec backend php artisan migrate
```

### Testes

A suíte utiliza uma instância separada do PostgreSQL. A conexão de testes é fornecida ao container pelas variáveis `TEST_DB_*` do `.env` da raiz.

```bash
docker compose --profile test run --rm backend-test
```

### Encerrar os serviços

```bash
docker compose down
```

Esse comando preserva o volume do banco de desenvolvimento.

## Estrutura

```text
.
├── backend/              # Aplicação Laravel
│   ├── docker/           # Inicialização do container
│   ├── app/
│   ├── database/
│   ├── routes/
│   │   ├── api.php       # Rotas HTTP da API
│   │   └── console.php   # Comandos Artisan
│   └── tests/
├── compose.yaml
├── CHANGELOG.md
└── README.md
```

O Dockerfile possui alvos separados para desenvolvimento e produção. A imagem de desenvolvimento inclui Composer e dependências de teste; a imagem de produção não inclui Composer nem dependências de desenvolvimento.

## Regras de negócio

### Empresas

- Cadastro, consulta, edição e listagem.
- Controle de status ativo e inativo.
- Exclusão lógica, restauração e exclusão definitiva.
- CNPJ único, inclusive entre registros excluídos logicamente.
- Empresas inativas ou excluídas não podem receber novos produtos.

### Produtos

- Cadastro, consulta, edição e listagem.
- Vínculo obrigatório com uma empresa.
- Controle de status ativo e inativo.
- Exclusão lógica, restauração e exclusão definitiva.
- Código interno único dentro da empresa.
- Produtos não podem ser criados, editados ou ativados quando a empresa estiver inativa ou excluída.

### Status e exclusão

O status operacional e a exclusão lógica são tratados como estados independentes.

- Inativar uma empresa também inativa seus produtos.
- Reativar uma empresa não reativa os produtos automaticamente.
- Excluir uma empresa logicamente também exclui seus produtos.
- Restaurar uma empresa recupera somente os produtos excluídos junto com ela.
- Produtos excluídos individualmente permanecem excluídos após a restauração da empresa.
- Uma empresa com produtos vinculados não pode ser excluída definitivamente, inclusive quando os produtos estão excluídos logicamente.
- A exclusão definitiva exige exclusão lógica prévia e confirmação explícita.

## Histórico de mudanças

As alterações de cada versão são registradas no [CHANGELOG.md](CHANGELOG.md).
