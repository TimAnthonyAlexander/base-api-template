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
php bin/console serve
```

Your API will be available at `http://localhost:7879`.
You can change host and port in the .env file.

### Console commands are run using:

```bash
php bin/console
```

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
