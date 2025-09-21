# BaseAPI Project

This is the "empty" template project using baseapi.
Creating a new project with baseapi will use this as a starter template.

## Quick Start

Create a new baseapi project "my-api" using Composer:

```bash
composer create-project baseapi/baseapi-template my-api
cd my-api
```

Copy the environment example file and start the server

```bash
cp .env.example .env
php mason serve
```

Your API will be available at `http://localhost:7879`.
You can change host and port in the .env file.

### Console commands are run using:

```bash
php mason
```

## Git Hooks

This template includes pre-commit hooks that automatically check code quality:

- **PHP Syntax Check** - Validates all staged PHP files
- **PHPStan Analysis** - Static code analysis (if available)
- **Tests** - Runs PHPUnit tests when core files change
- **Code Quality** - Prevents debugging functions in commits
- **File Size Warnings** - Alerts for large files

### Setup

Hooks are automatically installed when creating a new project. To reinstall manually:

```bash
composer setup-hooks
```

### Bypass Hook (Not Recommended)

```bash
git commit --no-verify -m "Skip pre-commit checks"
```

See `.githooks/README.md` for detailed documentation.

## Dependency Injection Example

This template demonstrates BaseAPI's dependency injection system:

### EmailService Example

The `SignupController` shows how to inject services:

```php
class SignupController extends Controller
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function post(): JsonResponse
    {
        // ... user creation logic ...
        
        // Use injected service
        $this->emailService->sendWelcome($user->email, $user->name);
        
        return JsonResponse::ok($user->jsonSerialize());
    }
}
```

### Service Provider

Services are registered in `app/Providers/AppServiceProvider.php`:

```php
public function register(ContainerInterface $container): void
{
    $container->singleton(EmailService::class);
    $container->singleton(UserProvider::class, SimpleUserProvider::class);
}
```

### Configuration

Providers are registered in `config/app.php`:

```php
'providers' => [
    \App\Providers\AppServiceProvider::class,
],
```

## Documentation

For full framework documentation, features, and usage examples, see:
- **[BaseAPI Repository](https://github.com/timanthonyalexander/base-api)** - Complete documentation

---

**BaseAPI** - The tiny, KISS-first PHP 8.4 framework that gets out of your way.
