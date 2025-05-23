# Laravel Backend Setup

This document provides instructions for setting up the Laravel backend API.

## Installation & Setup

Follow these steps to get the Laravel backend up and running on your local machine.

### Backend Setup (Laravel)

1. **Navigate to the `backend` directory:**

```cd backend```

2. **Install PHP dependencies using Composer:**

```composer install```


*If you are using Laravel Sail, you would run:*

```./vendor/bin/sail composer install```


3. **Copy the environment file:**

```cp .env.example .env```


4. **Generate the application key:**

```php artisan key:generate```


5. **Configure your database:**
Open the newly created `.env` file and update your database connection details (e.g., `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

6. **Run database migrations:**
This will create the necessary tables in your database.

```php artisan migrate```


7. **Seed the database with initial data:**
First, seed the CSV data (e.g., pizza types, pizzas):

```php artisan db:seed --class=CsvImportSeeder```


Then, seed the default user data:

```php artisan db:seed --class=UserSeeder```


*(Alternatively, to run all seeders defined in `DatabaseSeeder.php`):*

```php artisan db:seed```


8. **Start the Laravel development server:**

```php artisan serve```


The backend API will typically be accessible at `http://127.0.0.1:8000`.
*If you are using Laravel Sail, you would start Sail (which includes the server):*

```./vendor/bin/sail up -d```

Login Credentials after seeding

```
admin@pizzasales.com
admin123
```
