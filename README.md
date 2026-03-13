# API RESTful + Tela de Estoque (Laravel)

Sistema simples de estoque com API REST e uma tela web para cadastrar, editar e excluir produtos. Ao abrir a URL do projeto (`/`), voce cai direto na tela de estoque.

## Funcionalidades
- Tela web de estoque (CRUD de produtos).
- API REST em `/api/v1`.
- Controle de estoque com movimentos e cancelamento de venda.
- Relatorio de vendas (total, receita e media).
- Paginacao nas listagens.

## Tecnologias
- Laravel 9
- PHP 8.0+
- PostgreSQL (recomendado no Render) ou MySQL

## Como rodar localmente
1. Instale dependencias:
   ```bash
   composer install
   ```
2. Copie o `.env`:
   ```bash
   cp .env.example .env
   ```
3. Configure o banco no `.env`.
4. Gere a chave da app:
   ```bash
   php artisan key:generate
   ```
5. Rode as migrations:
   ```bash
   php artisan migrate
   ```
6. Suba o servidor:
   ```bash
   php artisan serve
   ```

Abra `http://localhost:8000` para usar a tela de estoque.

## Endpoints da API
Prefixo: `/api/v1`  
Autenticacao: **desabilitada por padrao**.

### Produtos
| Metodo | Endpoint | Descricao |
|---|---|---|
| GET | `/api/v1/products` | Lista produtos (paginado) |
| GET | `/api/v1/products/{id}` | Busca produto |
| POST | `/api/v1/products` | Cria produto |
| PUT | `/api/v1/products/{id}` | Atualiza produto |
| DELETE | `/api/v1/products/{id}` | Exclui produto |

### Vendas
| Metodo | Endpoint | Descricao |
|---|---|---|
| GET | `/api/v1/sales` | Lista vendas (paginado) |
| GET | `/api/v1/sales/{id}` | Busca venda |
| POST | `/api/v1/sales` | Cria venda e reduz estoque |
| DELETE | `/api/v1/sales/{id}` | Cancela venda e repoe estoque |

### Relatorios
| Metodo | Endpoint | Descricao |
|---|---|---|
| GET | `/api/v1/reports/sales-summary` | Resumo de vendas |

## Observacoes importantes
- O `total_amount` da venda e calculado no servidor.
- Cancelar venda nao remove o registro, apenas marca `status=cancelled`.

## Deploy no Render (Docker)
O projeto ja possui `Dockerfile`, `conf/nginx/nginx-site.conf` e `scripts/00-laravel-deploy.sh`.

Variaveis recomendadas no Render:
```
APP_KEY=base64:...
APP_ENV=production
APP_DEBUG=false
APP_URL=https://SEU-SERVICO.onrender.com
LOG_CHANNEL=stderr
DB_CONNECTION=pgsql
DATABASE_URL=postgres://usuario:senha@host:5432/banco
```

Depois do deploy, rode:
```bash
php artisan migrate --force
```

## Licenca
MIT
