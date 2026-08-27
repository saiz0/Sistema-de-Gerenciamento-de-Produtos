# Sistema de Gerenciamento de Produtos

Sistema para cadastro e manutenção de empresas fornecedoras e seus respectivos produtos.

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
