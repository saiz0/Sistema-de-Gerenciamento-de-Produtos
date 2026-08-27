# Frontend

Interface do Sistema de Gerenciamento de Produtos construída com Vue, TypeScript e Vite.

## Execução

O frontend é executado exclusivamente pelo Docker Compose. Na raiz do projeto, inicie o ambiente:

```bash
docker compose up -d --build
```

A interface estará disponível em `http://localhost:5173`.

As dependências são instaladas na imagem, armazenadas no volume `frontend-node-modules` e sincronizadas pelo container durante a inicialização. Não é necessário instalar Node.js ou pnpm na máquina.

## Comandos

Execute todos os comandos a partir da raiz do projeto:

```bash
docker compose exec frontend pnpm type-check
docker compose exec frontend pnpm test
docker compose exec frontend pnpm build
```

Quando `package.json` ou `pnpm-lock.yaml` mudar, reinicie o serviço para sincronizar as dependências:

```bash
docker compose restart frontend
```

## Estrutura

```text
src/
├── app/          # Configuração da aplicação e roteamento
├── components/   # Atoms, molecules e organisms do Atomic Design
├── entities/     # Tipos, contratos, validações e regras por entidade
├── pages/        # Páginas associadas às rotas
├── shared/       # API, feedback, utilitários e validações comuns
└── styles/       # Tokens, normalização e estilos globais
```

Os estilos visuais permanecem em arquivos CSS externos aos componentes Vue.

## Regras de interface

- Empresas inativas ou excluídas não aparecem no seletor de vínculo de produtos.
- Produtos só podem ser editados ou ativados quando a empresa vinculada está ativa e não excluída.
- A exclusão definitiva de empresa só é exibida após confirmar que não há produtos vinculados, inclusive excluídos.
- Registros excluídos mantêm visível o status operacional anterior.
- Inativar empresa informa o impacto sobre produtos e que a reativação não é automática.
- Exclusões definitivas exigem confirmação com aviso de irreversibilidade.

Erros locais aparecem junto ao campo correspondente. Erros retornados pela API são preservados em português e erros inesperados utilizam mensagens genéricas sem detalhes internos.

## Testes

Os testes usam o Node existente na imagem e não exigem ferramentas instaladas localmente:

```bash
docker compose --profile test build frontend-test
```

A suíte cobre validações, políticas de visibilidade das ações, serialização de filtros, erros da API e confirmação explícita da exclusão definitiva.
