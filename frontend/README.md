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
├── pages/        # Páginas associadas às rotas
└── styles/       # Tokens, normalização e estilos globais
```

Os estilos visuais permanecem em arquivos CSS externos aos componentes Vue.
