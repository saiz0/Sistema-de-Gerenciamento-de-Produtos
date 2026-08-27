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
- API REST completa para cadastro, consulta, listagem, alteração, ativação, inativação, exclusão lógica, restauração e exclusão definitiva de produtos.
- Objetos de valor e validadores para preço e código interno de produtos.
- Filtros de produtos por nome, status, empresa e situação de exclusão, com paginação.
- Regras transacionais de inativação, exclusão e restauração em cascata entre empresas e produtos.
- Testes unitários e de integração para produtos e para o ciclo de vida conjunto com empresas.
- Mensagens de validação do Laravel em português do Brasil.
- Workflow de integração contínua para testes unitários e de integração com PostgreSQL isolado.
- Seeders idempotentes com 10 empresas e 100 produtos por empresa para demonstração e desenvolvimento.
- Aplicação frontend em Vue 3, TypeScript e Vite.
- Serviço Docker para desenvolvimento do frontend com atualização automática.
- Sincronização automática das dependências do frontend ao iniciar o container de desenvolvimento.
- Imagem multiestágio do frontend para dependências, desenvolvimento, build e produção com Nginx.
- Estrutura inicial de estilos externos com tokens visuais, normalização e estilos por componente.
- Interface responsiva em Atomic Design para gerenciamento completo de empresas e produtos.
- Filtros, paginação e acesso explícito a registros excluídos no frontend.
- Formulários com validações locais e associação de erros da API aos campos correspondentes.
- Políticas de interface para exibir ações somente quando permitidas pelas regras de negócio.
- Feedback visual de carregamento, sucesso, erro, listagem vazia e página não encontrada.
- Confirmações com avisos específicos para operações em cascata e exclusões irreversíveis.
- Testes unitários e de integração do frontend executados no Docker.
- Workflow de integração contínua para tipagem, testes e imagem de produção do frontend.
- Logotipo e identidade visual Horizon aplicados ao cabeçalho da interface.

### Changed

- Dockerfile dividido em estágios independentes de dependências, desenvolvimento e produção.
- Conexão padrão da aplicação alterada de SQLite para PostgreSQL.
- Imagem de produção configurada sem Composer e sem dependências de desenvolvimento.
- Aplicação configurada para carregar exclusivamente rotas de API e console.
- Respostas de exceção configuradas para utilizar JSON em todas as rotas.
- Configuração do Docker centralizada no `.env` da raiz, sem credenciais ou endereços fixos no Compose e no PHPUnit.
- Autoload PSR-4 configurado para as camadas da aplicação em `src/`.
- Imagem PHP preparada com a extensão `mbstring` para validação segura de texto Unicode.
- Contrato OpenAPI ampliado com todos os endpoints e schemas de produtos.
- Unicidade de e-mail das empresas garantida inclusive entre registros excluídos logicamente.
- Respostas inesperadas protegidas contra exposição de SQL, stack trace e detalhes internos.
- Pipeline configurado para validar Docker Compose, contrato OpenAPI e construção da imagem de produção.
- Status operacional e exclusão lógica apresentados como dimensões independentes na interface.

### Removed

- Vite, NPM e arquivos de frontend do esqueleto interno do Laravel.
- Assets CSS e JavaScript do esqueleto Laravel.
- View de boas-vindas e arquivo de rotas web.
- Comandos de instalação e build do frontend no `composer.json`.

### Fixed

- Permissões de escrita em `storage` e `bootstrap/cache` durante a inicialização do container.
- Execução do processo Laravel como usuário não privilegiado após a preparação dos diretórios.
- Atualização automática do Vite em volumes Docker no Windows por meio de polling configurável.

## [0.1.0] - 2026-08-26

### Added

- Documentação inicial com escopo e regras principais do sistema.
- Estrutura inicial do changelog.

[Unreleased]: https://github.com/saiz0/Sistema-de-Gerenciamento-de-Produtos/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/saiz0/Sistema-de-Gerenciamento-de-Produtos/releases/tag/v0.1.0
