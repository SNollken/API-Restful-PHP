# Store API

A RESTful API for a store management system, built with Laravel.

## Features

- Product management (CRUD)
- Sale processing with stock management
- Sales reports
- Authentication via Sanctum
- Input validation
- Rate limiting
- CORS restrictions
- Policies for sensitive actions

## Installation

1. Clone the repository:
   ```bash
   git clone <url-do-repositorio>
   cd apirest
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Configure the environment:
   - Copy `.env.example` to `.env`
   - Configure the database in `.env`

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Start the server:
   ```bash
   php artisan serve
   ```

## Configuration

- Laravel 9+
- PHP 8.0+
- MySQL/PostgreSQL

## API Endpoints

### Authentication

All endpoints require authentication via Sanctum.

### Products

- `GET /api/v1/products` - List all products (paginated)
- `GET /api/v1/products/{id}` - Get a specific product
- `POST /api/v1/products` - Create a product
- `PUT /api/v1/products/{id}` - Update a product
- `DELETE /api/v1/products/{id}` - Delete a product

### Sales

- `GET /api/v1/sales` - List all sales (paginated)
- `GET /api/v1/sales/{id}` - Get a specific sale
- `POST /api/v1/sales` - Create a sale
- `DELETE /api/v1/sales/{id}` - Cancel a sale

### Reports

- `GET /api/v1/reports/sales-summary` - Get sales summary

## Usage Examples

### Authenticate

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'
```

### Create Product

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Product Name",
    "description": "Product Description",
    "price": 99.99,
    "stock": 10
  }'
```

### Create Sale

```bash
curl -X POST http://localhost:8000/api/v1/sales \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "total_amount": 199.98,
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "unit_price": 99.99
      }
    ]
  }'
```

### Get Sales Summary

```bash
curl -X GET http://localhost:8000/api/v1/reports/sales-summary \
  -H "Authorization: Bearer {token}"
```

## Validation

### Product

- `name`: required, string, max 255 characters
- `description`: nullable, string
- `price`: required, numeric, min 0
- `stock`: required, integer, min 0

### Sale

- `total_amount`: required, numeric, min 0
- `items`: required, array
- `items.*.product_id`: required, exists in products table
- `items.*.quantity`: required, integer, min 1
- `items.*.unit_price`: required, numeric, min 0

## Project Structure

- `app/Http/Controllers/` - API controllers
- `app/Services/` - Business logic
- `app/Repositories/` - Database access
- `app/Http/Resources/` - JSON formatting
- `app/Http/Requests/` - Input validation
- `app/Policies/` - Authorization policies
- `app/Models/` - Eloquent models
- `database/migrations/` - Database migrations
- `database/factories/` - Model factories
- `tests/Feature/` - Feature tests

## Testing

Run the tests:

```bash
php artisan test
```

## License

MIT
