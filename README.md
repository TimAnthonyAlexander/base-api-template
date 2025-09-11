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
   curl http://localhost:8000/health
   ```

## Available Endpoints

- `GET /health` - Health check endpoint
- `POST /auth/login` - Login endpoint
- `POST /auth/logout` - Logout endpoint (requires auth)
- `GET /me` - Get current user info (requires auth)

## Development Commands

- `php bin/console serve` - Start development server
- `php bin/console make:controller NameController` - Generate controller
- `php bin/console make:model Name` - Generate model
- `php bin/console migrate:generate` - Generate migration plan
- `php bin/console migrate:apply` - Apply migrations
- `php bin/console types:generate` - Generate TypeScript types

## Documentation

For full documentation, visit the [BaseAPI repository](https://github.com/timanthonyalexander/base-api).

---

**BaseAPI** - The tiny, KISS-first PHP 8.4 framework that gets out of your way.
