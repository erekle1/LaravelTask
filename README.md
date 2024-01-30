
# Laravel  API

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP >= 8.1
- Composer ([Installation Guide](https://getcomposer.org/download/))
- MySQL or another compatible database

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/your-project.git
   cd LaravelTask
   ```

2. Install project dependencies using Composer:

   ```bash
   composer install
   ```

3. Create a `.env` file by copying the `.env.example` file:

   ```bash
   cp .env.example .env
   ```

4. Configure your database connection by editing the `.env` file:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   ```

5. Generate an application key:

   ```bash
   php artisan key:generate
   ```

6. Run database migrations to create the required tables:

   ```bash
   php artisan migrate 
   ```

7. Start the development server:

   ```bash
   php artisan serve
   ```

8. Your Laravel API should now be running at [http://localhost:8000](http://localhost:8000).

## API Routes

- POST /api/addProductInCart: Add a product to the user's cart.
- POST /api/removeProductFromCart: Remove a product from the user's cart.
- POST /api/setCartProductQuantity: Set the quantity of a product in the user's cart.
- GET /api/getUserCart: Retrieve the user's cart with discounts applied.

## API Documentation

For API documentation, you can use tools like Postman or Swagger. Import the provided Postman collection to explore and test the API endpoints.

- [Postman Collection](https://github.com/erekle1/LaravelTask/blob/master/postman-collection.json)

## Seeders and Fake Data

To populate the database with fake data, you can use Laravel seeders and factories. Run the following commands to generate fake data:

```bash
php artisan db:seed
```

## Testing

You can run tests to ensure the functionality of the API. Use PHPUnit for testing:

```bash
php artisan test
```



