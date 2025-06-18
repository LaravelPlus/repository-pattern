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
