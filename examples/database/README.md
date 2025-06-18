# Example SQLite Database Setup

This directory contains an example SQLite database setup for testing the repository pattern implementation.

## Structure

- `database.php` - SQLite database configuration
- `database.sqlite` - The SQLite database file (will be created when migrations run)
- `create_users_table.php` - Example migration for users table

## Usage

1. Make sure you have SQLite installed on your system
2. The database file will be automatically created when you run the migrations
3. Use this database configuration in your tests or examples

## Example Repository Usage

```php
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection(require __DIR__ . '/database.php');
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Run migrations
require __DIR__ . '/create_users_table.php';

// Now you can use your repositories with this database
```

## Notes

- This is a simple example setup for testing purposes
- The database file is in `.gitignore` by default
- Remember to handle database cleanup in your tests 