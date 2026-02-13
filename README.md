# MEET Portal (Nextcloud App)

App customizado para Nextcloud que entrega um portal de cliente para o metodo MEET:

- Etapa atual
- Progresso
- Tarefas pendentes
- Documentos principais

## Estrutura

```text
apps/meet_portal/
├── appinfo/
├── lib/
├── src/
├── templates/
├── package.json
└── webpack.js
```

## Build frontend

```bash
npm install
npm run build
```

O build gera o bundle usado por `templates/main.php`.

## Ativacao no Nextcloud

No diretorio raiz do Nextcloud:

```bash
php occ app:enable meet_portal
```

Para desativar:

```bash
php occ app:disable meet_portal
```

## Rotas disponiveis

- `GET /apps/meet_portal/`
- `GET /apps/meet_portal/api/dashboard`
- `POST /apps/meet_portal/api/task/{id}/complete`

## Dados MVP

Quando nao houver tabelas/dados da app no banco, o backend usa fallback mock:

- Cliente: Empresa Demo
- Fase: Mapear
- Progresso: 20%
- Tarefas:
  - Enviar documentos financeiros
  - Agendar entrevista

Arquivos sao lidos de:

`/Clientes/{client_name}/Portal/`

Se a pasta nao existir, a API retorna um arquivo mock (`Proposta.pdf`).
