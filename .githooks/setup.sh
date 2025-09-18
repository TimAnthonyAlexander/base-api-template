#!/bin/bash

# Setup script for BaseAPI git hooks
# This script copies hooks from .githooks/ to .git/hooks/ and makes them executable

set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_status "Setting up BaseAPI git hooks..."

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    print_error "Not in a git repository. Please run this from the project root."
    exit 1
fi

# Create .git/hooks directory if it doesn't exist
if [ ! -d ".git/hooks" ]; then
    mkdir -p ".git/hooks"
    print_status "Created .git/hooks directory"
fi

# Copy hooks from .githooks to .git/hooks
HOOKS_COPIED=0

for hook in .githooks/*; do
    if [ -f "$hook" ] && [ "$(basename "$hook")" != "setup.sh" ]; then
        hook_name=$(basename "$hook")
        target=".git/hooks/$hook_name"
        
        # Backup existing hook if it exists and is different
        if [ -f "$target" ] && ! cmp -s "$hook" "$target"; then
            backup="$target.backup.$(date +%Y%m%d_%H%M%S)"
            cp "$target" "$backup"
            print_warning "Backed up existing $hook_name to $(basename "$backup")"
        fi
        
        # Copy and make executable
        cp "$hook" "$target"
        chmod +x "$target"
        
        print_success "Installed $hook_name hook"
        HOOKS_COPIED=$((HOOKS_COPIED + 1))
    fi
done

if [ $HOOKS_COPIED -eq 0 ]; then
    print_warning "No hooks found to install"
else
    print_success "Successfully installed $HOOKS_COPIED git hook(s)"
fi

# Test the pre-commit hook
if [ -f ".git/hooks/pre-commit" ]; then
    print_status "Testing pre-commit hook..."
    
    # Create a temporary PHP file to test
    echo "<?php echo 'test';" > /tmp/test_hook.php
    
    if php -l /tmp/test_hook.php > /dev/null 2>&1; then
        print_success "Pre-commit hook is working correctly"
    else
        print_error "Pre-commit hook test failed"
    fi
    
    rm -f /tmp/test_hook.php
fi

print_success "Git hooks setup complete! 🎉"
print_status "The pre-commit hook will now run automatically before each commit."
print_status "To bypass the hook temporarily, use: git commit --no-verify"

exit 0

