# LaravelPlus Repository Pattern

A Laravel package that helps you organize all your Eloquent queries for each model in dedicated repository classes, making your codebase more maintainable, testable, and clean. Centralize your data access logic in one spot for each model, following best practices for modern Laravel development.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravelplus/repository-pattern.svg?style=flat-square)](https://packagist.org/packages/laravelplus/repository-pattern)
[![Total Downloads](https://img.shields.io/packagist/dt/laravelplus/repository-pattern.svg?style=flat-square)](https://packagist.org/packages/laravelplus/repository-pattern)
![GitHub Actions](https://github.com/laravelplus/repository-pattern/actions/workflows/main.yml/badge.svg)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require laravelplus/repository-pattern
```

## Usage

```php
// Usage description here
```

## Repository Pattern Usage

The repository pattern helps you keep all your Eloquent queries for a model in one place, making your codebase more organized and easier to maintain. With this package, you can create repositories in `app/Repositories` and inject them wherever you need.

### Example: `app/Repositories/UserRepository.php`

```php
namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function all()
    {
        return User::all();
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    // Add more query methods as needed
}
```

### Using the Repository in a Controller

```php
use App\Repositories\UserRepository;

class UserController extends Controller
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function index()
    {
        $users = $this->users->all();
        return view('users.index', compact('users'));
    }
}
```

> **Tip:** Place all your model queries in their respective repositories under `app/Repositories` to keep your code clean and maintainable.

### Testing

```bash
composer test
```

### Code Style: Pint

This package uses [Laravel Pint](https://laravel.com/docs/10.x/pint) for code style fixing. To automatically fix code style issues, run:

```bash
composer pint
```

### Static Analysis: PHPStan

This package uses [PHPStan](https://phpstan.org/) for static analysis. To run PHPStan, use:

```bash
composer phpstan
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email info@after.si instead of using the issue tracker.

## Credits

-   [Nejc Cotic](https://github.com/laravelplus)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).

## Modern Repository Example with Traits

You can enhance your repositories by mixing in traits for extra features like soft deletes, caching, and multi-database support.

### Example: `app/Repositories/UserRepository.php`

```php
namespace App\Repositories;

use App\Models\User;
use Laravelplus\RepositoryPattern\BaseRepository;
use Laravelplus\RepositoryPattern\Traits\SoftDeletes;
use Laravelplus\RepositoryPattern\Traits\Cacheable;
use Laravelplus\RepositoryPattern\Traits\MultiDatabase;

class UserRepository extends BaseRepository implements \Laravelplus\RepositoryPattern\Contracts\MultiDatabaseInterface
{
    use SoftDeletes, Cacheable, MultiDatabase;

    protected static string $modelClass = User::class;
    // Optionally configure $table, $primaryKey, $connection, etc.
}
```

### Using Trait Methods

```php
$userRepo = new UserRepository();
$userRepo->softDelete($userId); // Soft delete a user
$userRepo->restore($userId);    // Restore a soft-deleted user
$userRepo->cacheAll(30);        // Cache all users for 30 minutes
$userRepo->runOnConnection('mysql2', fn($db) => $db->table('users')->get());
```

## Available Traits

- `SoftDeletes`: Adds soft delete, restore, and onlyTrashed methods.
- `Cacheable`: Adds cacheAll for caching results.
- `Loggable`: Adds logAction for logging repository actions.
- `Eventable`: Adds fireEvent for dispatching events.
- `ValidatesData`: Adds validate for validating data before create/update.
- `Searchable`: Adds search for column-based LIKE search.
- `Sortable`: Adds sortBy for sorting results.
- `HasRelationships`: Adds withRelations for eager loading relationships.
- `MultiDatabase`: Adds runOnConnection and crossConnectionQuery for multi-database support.

Mix and match these traits in your repositories as needed!

## Repository Generator Command

You can quickly generate repository classes using the built-in Artisan command:

### Simple Repository

Generate a basic repository (no traits or interface):

```bash
php artisan make:repository User
```

This uses the `stubs/repository.simple.stub` template.

### Repository with Traits and Interface

Generate a repository with traits and/or an interface:

```bash
php artisan make:repository User --traits=SoftDeletes,Cacheable --interface=MultiDatabaseInterface
```

This uses the `stubs/repository.stub` template and will insert the specified traits and interface.

### Customizing Stubs

You can publish the stubs to your application and customize them as needed:

```bash
php artisan vendor:publish --tag=config
```

This will copy the stubs to your `stubs/` directory, where you can edit them to fit your project's needs.
