## Project setup (API)

Este documento descreve os comandos disponíveis para gerenciar migrações, controladores e classes no seu projeto.

### Gerar Migração
```bash
php console generate:migration DESCRIPTIVE_NAME
```
Substitua DESCRIPTIVE_NAME por um nome descritivo para a migração.

### Executar as Migrações
```bash
php console migrate
```

### Reverter Migrações
Para reverter a última migração, use:
```bash
php console rollback
```

### Reverter uma Migração Específica
```bash
php console rollback --name:NAME_MIGRATION
```
Substitua NAME_MIGRATION pelo nome do arquivo de migração que você deseja reverter.

### Reverter Várias Migrações
Para reverter as últimas N migrações, use:

```bash
php console rollback --order:N
```
Substitua N pelo número de migrações que você deseja reverter.