# Contributing to nijidigital/phpstan-rules

Thank you for your interest in contributing to this project! This guide will help you set up the development environment and understand our contribution process.

## Development Setup

### Prerequisites

- **PHP 8.2+** - This project requires PHP 8.2 or higher
- **Composer** - For dependency management
- **Docker** (recommended) - For consistent development environment
- **Task** (optional) - Task runner for common development tasks

### Option 1: Docker Development (Recommended)

The project includes Docker configuration for a consistent development environment.

1. **Fork and clone the repository:**
   ```bash
   # Fork the repository on GitHub first, then clone your fork
   git clone https://github.com/YOUR_USERNAME/phpstan-rules.git
   cd phpstan-rules
   git checkout v1.x

   # Add the original repository as upstream
   git remote add upstream https://github.com/nijidigital/phpstan-rules.git
   ```

2. **Start the development environment:**
   ```bash
   # Build and run the Docker container
   task cli
   ```

   This will:
   - Build the Docker image if needed
   - Install Composer dependencies
   - Start an interactive shell in the container

3. **Available Task commands:**
   ```bash
   task --list          # List all available tasks
   task test            # Run PHPUnit tests
   task quality         # Run code quality checks (Rector, PHP-CS-Fixer, PHPStan)
   task build           # Build Docker image
   task run             # Start container in background
   ```

### Option 2: Local Development

If you prefer to develop without Docker:

1. **Fork, clone and setup:**
   ```bash
   # Fork the repository on GitHub first, then clone your fork
   git clone https://github.com/YOUR_USERNAME/phpstan-rules.git
   cd phpstan-rules
   git checkout v1.x

   # Add the original repository as upstream
   git remote add upstream https://github.com/nijidigital/phpstan-rules.git

   # Install dependencies
   composer install
   ```

2. **Run tests:**
   ```bash
   vendor/bin/phpunit
   ```

3. **Run code quality checks:**
   ```bash
   # Code style fixing
   vendor/bin/php-cs-fixer fix --config .php-cs-fixer.php --diff

   # Refactoring
   vendor/bin/rector process --config rector.php

   # Static analysis
   vendor/bin/phpstan analyse --level max --configuration phpstan.neon src tests
   ```

## Contributing Guidelines

### Before You Start

1. **Fork the repository** - Create your own fork of the project on GitHub
2. **Always start from the `v1.x` branch** - This is our main development branch
3. **Check existing issues** - Look for existing issues or discussions related to your contribution
4. **Create an issue first** - For new features or significant changes, please create an issue to discuss the approach

### Adding a New Rule

1. **Create the rule class** in `src/Rules/`:
   ```php
   <?php

   namespace NijiDigital\PhpStanRules\Rules;

   use PhpParser\Node;
   use PHPStan\Analyser\Scope;
   use PHPStan\Rules\Rule;
   use PHPStan\Rules\RuleErrorBuilder;

   /**
    * @implements Rule<Node\SomeNodeType>
    */
   class YourNewRule implements Rule
   {
       public function getNodeType(): string
       {
           return Node\SomeNodeType::class;
       }

       public function processNode(Node $node, Scope $scope): array
       {
           // Your rule logic here

           return [
               RuleErrorBuilder::message('Your error message')
                   ->build(),
           ];
       }
   }
   ```

2. **Register the rule** in `extension.neon`:
   ```yaml
   rules:
       - NijiDigital\PhpStanRules\Rules\YourNewRule
   ```

3. **Create test cases** in `tests/Rules/`:
   ```php
   <?php

   namespace NijiDigital\PhpStanRules\Tests\Rules;

   use NijiDigital\PhpStanRules\Rules\YourNewRule;
   use PHPStan\Rules\Rule;
   use PHPStan\Testing\RuleTestCase;

   /**
    * @extends RuleTestCase<YourNewRule>
    */
   class YourNewRuleTest extends RuleTestCase
   {
       protected function getRule(): Rule
       {
           return new YourNewRule();
       }

       public function testRule(): void
       {
           $this->analyse([__DIR__ . '/data/YourNewRule/test-cases.php'], [
               // Expected errors
           ]);
       }
   }
   ```

4. **Create test data** in `tests/Rules/data/YourNewRule/`:
   - Create PHP files with test cases showing both valid and invalid code
   - Use comments to document expected behavior

### Bug Fixes

1. **Create a failing test** that reproduces the bug
2. **Fix the issue** in the appropriate rule class
3. **Ensure the test passes** and no existing tests are broken
4. **Update documentation** if the fix changes behavior

### Pull Request Process

1. **Sync and branch from `v1.x`:**
   ```bash
   # Sync your fork with the upstream repository
   git checkout v1.x
   git pull upstream v1.x
   git push origin v1.x

   # Create your feature branch
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/your-bug-fix
   ```

2. **Make your changes** following the guidelines above

3. **Test your changes:**
   ```bash
   # Run all tests
   task test
   # or locally:
   vendor/bin/phpunit

   # Run quality checks
   task quality
   # or locally:
   vendor/bin/rector process --config rector.php --dry-run
   vendor/bin/php-cs-fixer fix --config .php-cs-fixer.php --dry-run --diff
   vendor/bin/phpstan analyse --level max --configuration phpstan.neon src tests
   ```

4. **Update documentation:**
   - Add your rule to the `README.md` with examples
   - Include both invalid (❌) and valid (✅) code examples
   - Document any configuration options

5. **Push and submit your pull request:**
   ```bash
   # Push your branch to your fork
   git push origin feature/your-feature-name
   ```

   Then create a pull request on GitHub:
   - **Source**: `YOUR_USERNAME/phpstan-rules` (your feature branch)
   - **Target**: `nijidigital/phpstan-rules` (the `v1.x` branch)
   - Provide a clear description of what your PR does
   - Link any related issues
   - Include examples of the rule in action

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add PHPDoc comments for classes and methods
- Include type hints where possible
- Write clear error messages that help users understand what to fix

### Testing

- Write comprehensive tests for your rules
- Test both positive and negative cases
- Include edge cases in your test data
- Ensure your tests are focused and don't test unrelated functionality

### Documentation

- Update the `README.md` with your new rule
- Include clear examples of what the rule catches
- Show alternative approaches for fixing violations
- Use emoji indicators (❌ for invalid, ✅ for valid)

## Getting Help

- **Issues**: Open an issue on GitHub for bugs or feature requests
- **Discussions**: Use GitHub Discussions for questions or general discussion
- **Code Review**: Don't hesitate to ask for feedback during the PR process

Thank you for contributing to make PHP codebases safer and more maintainable! 🚀
