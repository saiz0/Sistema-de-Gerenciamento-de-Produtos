# Changelog

As mudanças relevantes deste projeto serão registradas neste arquivo.

O formato segue o [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e o projeto adotará [Versionamento Semântico](https://semver.org/lang/pt-BR/) quando a primeira versão for publicada.

## [Unreleased]

### Added

- Estrutura inicial da aplicação com Laravel 13 e PHP 8.4.
- Serviço PostgreSQL 17 com volume persistente para desenvolvimento.
- Serviços isolados para execução dos testes com PostgreSQL temporário em `tmpfs`.
- Configuração do PHPUnit protegida contra o uso acidental do banco de desenvolvimento.
- Health check da aplicação disponível em `/up`.
- API REST versionada para cadastro, consulta, listagem, alteração, ativação, inativação, exclusão lógica, restauração e exclusão definitiva de empresas.
- Arquitetura DDD em `src/`, separando Domain, Application, Infrastructure e Presentation.
- Objetos de valor e validadores para CNPJ, e-mail e telefone.
- Respostas JSON padronizadas e catálogo reutilizável de mensagens genéricas.
- Filtros de empresas por nome, status e situação de exclusão, com paginação.
- Contrato OpenAPI 3.1 e serviço Swagger UI.
- Testes unitários de domínio e casos de uso da feature de empresas.
- Testes de integração da API utilizando o banco PostgreSQL isolado.

### Changed

- Dockerfile dividido em estágios independentes de dependências, desenvolvimento e produção.
- Conexão padrão da aplicação alterada de SQLite para PostgreSQL.
- Imagem de produção configurada sem Composer e sem dependências de desenvolvimento.
- Aplicação configurada para carregar exclusivamente rotas de API e console.
- Respostas de exceção configuradas para utilizar JSON em todas as rotas.
- Configuração do Docker centralizada no `.env` da raiz, sem credenciais ou endereços fixos no Compose e no PHPUnit.
- Autoload PSR-4 configurado para as camadas da aplicação em `src/`.
- Imagem PHP preparada com a extensão `mbstring` para validação segura de texto Unicode.

### Removed

- Vite, NPM e arquivos de configuração do frontend.
- Assets CSS e JavaScript do esqueleto Laravel.
- View de boas-vindas e arquivo de rotas web.
- Comandos de instalação e build do frontend no `composer.json`.

### Fixed

- Permissões de escrita em `storage` e `bootstrap/cache` durante a inicialização do container.
- Execução do processo Laravel como usuário não privilegiado após a preparação dos diretórios.

## [0.1.0] - 2026-08-26

### Added

- Documentação inicial com escopo e regras principais do sistema.
- Estrutura inicial do changelog.

[Unreleased]: https://github.com/saiz0/Sistema-de-Gerenciamento-de-Produtos/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/saiz0/Sistema-de-Gerenciamento-de-Produtos/releases/tag/v0.1.0
