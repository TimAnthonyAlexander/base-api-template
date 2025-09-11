# BaseAPI Project

This is a project created using the BaseAPI framework template.

## Quick Start

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   # Edit .env with your settings
   ```

3. **Start the development server:**
   ```bash
   php bin/console serve
   ```

4. **Test your API:**
   ```bash
   curl http://localhost:7879/health
   ```

## Available Endpoints

- `GET /health` - Health check endpoint
- `POST /auth/login` - Login endpoint
- `POST /auth/logout` - Logout endpoint (requires auth)
- `GET /me` - Get current user info (requires auth)

## Development Commands

- `php bin/console serve` - Start development server (port 7879)
- `php bin/console make:controller NameController` - Generate controller
- `php bin/console make:model Name` - Generate model
- `php bin/console migrate:generate` - Generate migration plan
- `php bin/console migrate:apply` - Apply migrations
- `php bin/console types:generate` - Generate TypeScript types

## Framework

This template uses the [BaseAPI framework](https://packagist.org/packages/baseapi/baseapi) from Packagist:

```json
{
    "require": {
        "baseapi/baseapi": "dev-feature/split"
    }
}
```

The framework provides all core functionality while this template provides the project structure and example implementation.

## Configuration

The template is pre-configured for:
- **API Port**: 7879 (configurable via `APP_PORT`)
- **MySQL Port**: 7878 (configurable via `DB_PORT`)
- Session-based authentication
- Rate limiting on specific endpoints
- CORS handling for frontend integration

## Documentation

For full framework documentation, visit the [BaseAPI repository](https://github.com/timanthonyalexander/base-api).

---

**BaseAPI** - The tiny, KISS-first PHP 8.4 framework that gets out of your way.