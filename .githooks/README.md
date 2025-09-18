# Git Hooks for BaseAPI

This directory contains git hooks that help maintain code quality and consistency in your BaseAPI project.

## Available Hooks

### pre-commit

The pre-commit hook runs automatically before each commit and performs the following checks:

1. **PHP Syntax Check** - Validates syntax of all staged PHP files
2. **Composer Dependencies** - Ensures composer.lock is updated when composer.json changes
3. **PHPStan Analysis** - Runs static analysis on staged files (if available)
4. **Tests** - Runs PHPUnit tests when core files are modified (if available)
5. **Code Quality** - Checks for debugging functions (var_dump, die, etc.)
6. **File Size** - Warns about large files (>1MB)
7. **TODO/FIXME** - Warns about new TODO/FIXME comments

## Setup

### Automatic Setup (Recommended)

When you create a new BaseAPI project using `composer create-project`, the hooks are automatically installed.

You can also run the setup manually:

```bash
composer setup-hooks
```

### Manual Setup

```bash
# Make the setup script executable
chmod +x .githooks/setup.sh

# Run the setup script
./.githooks/setup.sh
```

## Usage

Once installed, the pre-commit hook runs automatically before each commit:

```bash
git add .
git commit -m "Your commit message"
# Hook runs automatically here
```

### Bypassing the Hook

If you need to bypass the pre-commit hook temporarily (not recommended):

```bash
git commit --no-verify -m "Your commit message"
```

## Customization

You can customize the hooks by editing the files in this directory:

- `pre-commit` - The main pre-commit hook script
- `setup.sh` - The setup script that installs hooks

After making changes, run the setup script again to update the installed hooks:

```bash
./.githooks/setup.sh
```

## Hook Details

### What the pre-commit hook checks:

- ✅ **PHP Syntax**: All staged `.php` files are checked for syntax errors
- ✅ **Static Analysis**: PHPStan analysis (if configured)
- ✅ **Tests**: PHPUnit tests (if core files changed)
- ✅ **Dependencies**: Composer lock file consistency
- ✅ **Code Quality**: No debugging functions in committed code
- ✅ **File Sizes**: Warns about large files
- ⚠️ **TODOs**: Warns about new TODO/FIXME comments

### Exit Codes

- `0` - All checks passed, commit proceeds
- `1` - One or more checks failed, commit is blocked

## Troubleshooting

### Hook not running

1. Check if the hook is installed: `ls -la .git/hooks/pre-commit`
2. Check if it's executable: `chmod +x .git/hooks/pre-commit`
3. Re-run setup: `composer setup-hooks`

### PHPStan/PHPUnit not found

The hooks gracefully skip PHPStan and PHPUnit checks if they're not available. To enable them:

```bash
# Install development dependencies
composer install --dev

# Or install them individually
composer require --dev phpstan/phpstan
composer require --dev phpunit/phpunit
```

### Hook is too strict

You can modify the `.githooks/pre-commit` file to adjust the checks according to your needs, then run the setup script again.

