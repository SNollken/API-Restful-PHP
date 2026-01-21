# Store API

A RESTful API for a store management system, built with Laravel. This API provides endpoints for managing products, processing sales, and generating sales reports.

## Features

- **Product Management**: Full CRUD operations for products (Create, Read, Update, Delete)
- **Sale Processing**: Create and cancel sales with automatic stock management
- **Stock Control**: Automatic stock reduction on sales and restoration on cancellations
- **Sales Reports**: Summary of total sales, revenue, and average sale value
- **Authentication**: Secure API access using Laravel Sanctum
- **Input Validation**: Comprehensive validation for all API requests
- **Rate Limiting**: Protection against excessive requests
- **CORS Restrictions**: Secure cross-origin resource sharing
- **Authorization Policies**: Fine-grained access control for sensitive actions

## Technologies Used

- **Laravel 9.x**: PHP framework for web applications
- **PHP 8.0+**: Server-side scripting language
- **MySQL/PostgreSQL**: Relational database management system
- **Laravel Sanctum**: Lightweight authentication system for APIs
- **PHPUnit**: Testing framework for PHP
- **Faker**: Library for generating fake data for testing

## Architecture

The project follows a layered architecture pattern:

```
Controller → Request → Service → Repository → Model
```

### Benefits of This Architecture:

1. **Separation of Concerns**: Each layer has a specific responsibility
2. **Maintainability**: Changes in one layer have minimal impact on others
3. **Testability**: Easy to mock dependencies and test components in isolation
4. **Scalability**: Clear structure makes it easier to add new features

### Layer Details:

- **Controllers**: Handle HTTP requests and responses
- **Requests**: Validate incoming data
- **Services**: Contain business logic
- **Repositories**: Handle database operations
- **Models**: Represent database entities and relationships

## Requirements

- PHP 8.0 or higher
- Composer
- MySQL or PostgreSQL
- Node.js (for frontend assets, if applicable)

## Installation

### Step-by-Step Guide:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd apirest
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies (if needed):**
   ```bash
   npm install
   ```

4. **Configure the environment:**
   - Copy `.env.example` to `.env`
   - Configure your database connection in `.env`
   - Generate application key:
     ```bash
     php artisan key:generate
     ```

5. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## Authentication

This API uses Laravel Sanctum for authentication.

### Obtaining a Token:

1. **Register a user** (if registration is enabled)
2. **Login to get a token:**
   ```bash
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email": "user@example.com", "password": "password"}'
   ```

### Using the Token:

Include the token in the `Authorization` header for all authenticated requests:

```bash
Authorization: Bearer {your-token}
```

## API Endpoints

All endpoints are prefixed with `/api/v1` and require authentication.

### Products

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/products` | List all products (paginated) |
| GET | `/api/v1/products/{id}` | Get a specific product |
| POST | `/api/v1/products` | Create a new product |
| PUT | `/api/v1/products/{id}` | Update a product |
| DELETE | `/api/v1/products/{id}` | Delete a product |

### Sales

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/sales` | List all sales (paginated) |
| GET | `/api/v1/sales/{id}` | Get a specific sale |
| POST | `/api/v1/sales` | Create a new sale |
| DELETE | `/api/v1/sales/{id}` | Cancel a sale |

### Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/reports/sales-summary` | Get sales summary report |

## Usage Examples

### Create a Product

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

**Response:**
```json
{
  "message": "Product created successfully!",
  "data": {
    "id": 1,
    "name": "Product Name",
    "description": "Product Description",
    "price": "99.99",
    "stock": 10
  }
}
```

### Create a Sale

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

**Response:**
```json
{
  "message": "Sale created successfully!",
  "data": {
    "id": 1,
    "total_amount": "199.98",
    "status": "completed"
  }
}
```

### Get Sales Summary

```bash
curl -X GET http://localhost:8000/api/v1/reports/sales-summary \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
  "message": "Sales summary retrieved successfully!",
  "data": {
    "total_sales": 5,
    "total_revenue": 999.9,
    "average_sale_value": 199.98
  }
}
```

## Validation Rules

### Product Validation

- `name`: Required, string, max 255 characters
- `description`: Optional, string
- `price`: Required, numeric, min 0
- `stock`: Required, integer, min 0

### Sale Validation

- `total_amount`: Required, numeric, min 0
- `items`: Required, array
- `items.*.product_id`: Required, must exist in products table
- `items.*.quantity`: Required, integer, min 1
- `items.*.unit_price`: Required, numeric, min 0

## Project Structure

```
app/
├── Http/
│   ├── Controllers/      # API controllers
│   ├── Requests/         # Input validation
│   └── Resources/        # JSON formatting
├── Models/              # Eloquent models
├── Policies/            # Authorization policies
├── Repositories/        # Database access
├── Services/            # Business logic
database/
├── factories/           # Model factories
├── migrations/          # Database migrations
└── seeders/             # Database seeders
tests/
├── Feature/             # Feature tests
└── Unit/                # Unit tests
```

## Testing

The project includes comprehensive tests for all major features.

### Running Tests

```bash
php artisan test
```

### Test Coverage

- Product CRUD operations
- Sale creation and cancellation
- Stock management
- Sales reports
- Authentication

## Database Schema

### Products Table

- `id`: Primary key
- `name`: Product name
- `description`: Product description (nullable)
- `price`: Product price
- `stock`: Current stock quantity
- `created_at`, `updated_at`: Timestamps

### Sales Table

- `id`: Primary key
- `total_amount`: Total sale amount
- `status`: Sale status (e.g., completed, cancelled)
- `created_at`, `updated_at`: Timestamps

### Sale Items Table

- `id`: Primary key
- `sale_id`: Foreign key to sales table
- `product_id`: Foreign key to products table
- `quantity`: Quantity sold
- `unit_price`: Price per unit at time of sale
- `created_at`, `updated_at`: Timestamps

### Stock Movements Table

- `id`: Primary key
- `product_id`: Foreign key to products table
- `quantity`: Quantity change (positive or negative)
- `movement_type`: Type of movement (e.g., sale, sale_cancellation)
- `description`: Movement description
- `created_at`, `updated_at`: Timestamps

## Security Features

- **Authentication**: All endpoints require valid Sanctum tokens
- **Authorization**: Policies control access to sensitive operations
- **Input Validation**: All requests are validated before processing
- **Rate Limiting**: Protection against brute force attacks
- **CORS**: Configured to restrict cross-origin requests

## Error Handling

The API returns appropriate HTTP status codes and error messages:

- `200 OK`: Successful request
- `201 Created`: Resource created successfully
- `202 Accepted`: Request accepted for processing
- `400 Bad Request`: Invalid input data
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `500 Internal Server Error`: Server-side error

## Future Improvements

- Add more comprehensive error handling
- Implement caching for frequently accessed data
- Add more detailed reporting features
- Implement product categories and tags
- Add user roles and permissions
- Implement API versioning strategy
- Add pagination controls to API responses
- Implement rate limiting per user

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
