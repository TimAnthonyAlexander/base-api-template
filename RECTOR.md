# Rector Integration

This project template uses [Rector](https://getrector.com/) to provide modern PHP 8.4+ code patterns and maintain high code quality standards.

## Configuration

The Rector configuration is opinionated to demonstrate best practices for new applications:

- **Language Level**: PHP 8.4 features and modernizations
- **Quality Sets**: Comprehensive code quality improvements
- **Standards**: PSR-4, coding style, naming conventions
- **Modern Patterns**: Constructor property promotion, readonly classes, strict types
- **Testing**: PHPUnit 11.x modernizations

## Usage

### Local Development

```bash
# Check for potential changes (dry-run, recommended)
composer rector

# Apply all changes
composer rector:fix
```

### CI Integration

The CI pipeline automatically runs Rector checks:

```bash
# CI-friendly output
composer rector:ci
```

If Rector finds code that needs updating, the CI will:
1. Fail the build
2. Generate a diff artifact showing required changes
3. Upload the artifact for review

## Pre-commit Integration (Optional)

Add Rector to your pre-commit hooks:

```bash
# .git/hooks/pre-commit
#!/bin/sh
composer rector --dry-run
if [ $? -ne 0 ]; then
    echo "❌ Rector found issues. Run 'composer rector:fix' to fix them."
    exit 1
fi
```

## Best Practices

1. **Run Rector regularly**: Integrate it into your development workflow
2. **Review changes**: While more aggressive than the framework, still review changes
3. **Leverage modern PHP**: The template showcases PHP 8.4+ patterns
4. **Maintain consistency**: Rector helps enforce consistent code style across your team

## Development Workflow

### New Features
1. Write your code
2. Run `composer rector` to check for improvements
3. Apply changes with `composer rector:fix`
4. Test and commit

### Code Reviews
- CI automatically checks for Rector compliance
- Failed builds include diff artifacts for easy review
- Apply suggested changes before merging

## Configuration Details

See `rector.php` for the complete configuration. The template setup includes:

- **Aggressive modernization**: Constructor property promotion, readonly patterns
- **Comprehensive quality rules**: Dead code removal, early returns, type declarations
- **Modern standards**: Latest PHP patterns and best practices
- **Performance optimizations**: Parallel processing with caching

This opinionated setup helps new projects start with clean, modern PHP code from day one.
