# Backend

API Laravel responsável pelas regras de negócio e pela persistência do sistema de gerenciamento de produtos.

## Tecnologias

- PHP 8.4 no container;
- Laravel 13;
- PostgreSQL 17;
- Composer 2;
- PHPUnit 12;
- OpenAPI 3.1 e Swagger UI.

O Laravel está configurado exclusivamente como API. O backend não possui views, Vite, NPM ou assets de frontend.

## Execução com Docker

O ambiente oficial de desenvolvimento deste projeto é o Docker Compose. Não é necessário instalar PHP, Composer ou PostgreSQL diretamente na máquina, e os comandos do Laravel não devem ser executados fora do container.

Todos os comandos desta seção devem ser executados na raiz do repositório, onde está o arquivo `compose.yaml`.

### Primeira execução

Crie os arquivos locais de ambiente:

```bash
cp .env.example .env
cp backend/.env.example backend/.env
```

O `.env` da raiz fornece ao Docker as portas, imagens e credenciais dos bancos. O `backend/.env` contém a configuração própria do Laravel. As variáveis de conexão definidas pelo Compose são injetadas no container e prevalecem sobre os valores locais do Laravel.

Construa as imagens e inicie os serviços:

```bash
docker compose up -d --build
```

Gere a chave da aplicação e execute as migrations:

```bash
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate
```

Serviços disponíveis após a inicialização:

- API: `http://localhost:8000`;
- health check: `http://localhost:8000/up`;
- Swagger UI: `http://localhost:8081`.

As portas podem ser alteradas no `.env` da raiz.

### Comandos Laravel e Composer

Artisan e Composer são executados pelo serviço `backend`:

```bash
docker compose exec backend php artisan route:list
docker compose exec backend php artisan migrate:status
docker compose exec backend composer install
docker compose exec backend composer dump-autoload
```

Após alterar os namespaces PSR-4 do `composer.json`, execute `composer dump-autoload` dentro do container. Depois de alterar dependências ou extensões PHP, reconstrua a imagem com `docker compose up -d --build`.

### Testes

A suíte de testes utiliza os serviços `backend-test` e `postgres-test`. O banco de testes é separado do banco de desenvolvimento e armazena seus dados em `tmpfs`, sendo descartado ao final da execução.

```bash
docker compose --profile test run --rm backend-test
```

Esse comando executa testes unitários e de integração sem gravar dados no PostgreSQL de desenvolvimento.

### Logs e encerramento

```bash
docker compose logs -f backend
docker compose down
```

O `docker compose down` preserva o volume do banco de desenvolvimento. A remoção desse volume não faz parte do fluxo normal, pois apaga os dados locais persistidos.

## Laravel na arquitetura

O Laravel permanece nas bordas da aplicação:

- `routes/api.php` registra as rotas e aplica o prefixo `/api`;
- Form Requests validam e normalizam a entrada HTTP;
- controllers convertem a requisição em DTOs e acionam casos de uso;
- service providers vinculam os contratos do domínio às implementações de infraestrutura;
- Eloquent implementa persistência e soft delete;
- `bootstrap/app.php` padroniza o tratamento das exceções em JSON;
- migrations versionam a estrutura do PostgreSQL.

Esse uso mantém os recursos produtivos do framework sem acoplar entidades e regras de negócio diretamente ao Laravel.

## Decisões de arquitetura

### Por que DDD

DDD pode tornar a estrutura mais extensa e, em alguns casos, mais verbosa. Neste projeto essa separação é intencional: cada classe possui uma responsabilidade pequena e explícita, enquanto as regras de negócio permanecem independentes do Laravel, do protocolo HTTP e do banco de dados.

Além dos benefícios tradicionais de manutenção e testes, essa organização favorece o desenvolvimento assistido por IA. Quanto menor e mais bem definido for o papel de uma classe, menor é o contexto necessário para compreender ou alterar seu comportamento. Isso reduz o acoplamento entre mudanças, facilita revisões e diminui a chance de uma alteração afetar partes não relacionadas do sistema.

A aplicação está organizada em quatro camadas:

- `Domain`: entidades, objetos de valor, enums e contratos de repositório;
- `Application`: casos de uso, DTOs, serviços e exceções da aplicação;
- `Infrastructure`: implementações técnicas, como Eloquent, repositórios e providers;
- `Presentation`: controllers, Form Requests, validators, recursos e respostas HTTP.

As dependências apontam para o domínio. Dessa forma, as regras centrais podem ser testadas sem inicializar o Laravel ou acessar o PostgreSQL.

### Objetos de valor em vez de tipos primitivos

Dados que possuem comportamento e regras próprias não são representados apenas por `string`. CNPJ, e-mail e telefone utilizam objetos de valor para centralizar:

- normalização;
- validação;
- formato interno;
- mensagens relacionadas à regra do valor;
- reutilização entre diferentes casos de uso.

Por exemplo, um `Cnpj` válido não é somente um texto com 14 caracteres. Ele precisa remover máscara, rejeitar sequências inválidas e verificar os dígitos calculados. Ao executar essa regra no construtor do objeto de valor, os casos de uso podem reutilizar a mesma implementação e o domínio passa a trabalhar com um valor já consistente.

Essa abordagem evita duplicar validações em controllers, services e casos de uso. Os Form Requests continuam responsáveis por validar a entrada HTTP e apresentar erros adequados ao cliente, enquanto os objetos de valor protegem o domínio independentemente da origem dos dados.

## Estrutura

```text
src/
├── Domain/
│   └── Company/
│       ├── Collections/
│       ├── Entities/
│       ├── Enums/
│       ├── Repositories/
│       └── ValueObjects/
├── Application/
│   └── Company/
│       ├── DTOs/
│       ├── Exceptions/
│       ├── Services/
│       └── UseCases/
├── Infrastructure/
│   ├── Persistence/Eloquent/
│   └── Providers/
└── Presentation/
    └── Http/
        ├── Controllers/
        ├── Requests/
        ├── Resources/
        ├── Responses/
        └── Validation/
```

Os namespaces dessas camadas são carregados via PSR-4 pelo Composer.
