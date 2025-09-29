# nijidigital/phpstan-rules

Custom PhpStan rules made with ❤️ by [Niji](https://www.niji.fr).

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```shell
composer require --dev nijidigital/phpstan-rules
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include phpstan.neon in your project's PHPStan config:

```
includes:
    - vendor/nijidigital/phpstan-rules/phpstan.neon
```
</details>

## Existing rules

### `NoRelativeDatetime`

Prevents usage of relative date/time strings and empty constructors in `DateTime` and `DateTimeImmutable` to ensure consistent and testable date handling.

#### Examples

❌ **Invalid:**
```php
// Empty constructors
$now = new \DateTime();
$now = new \DateTimeImmutable();

// Relative date strings
$yesterday = new \DateTime('yesterday');
$nextMonth = new \DateTime('next month');
$twoHoursLater = new \DateTimeImmutable('+2 hours');
```

✅ **Valid alternatives:**
```php
// Use absolute dates for fixed dates
$fixedDate = new \DateTime('2025-07-01 13:12:11');

// Use ClockInterface for current time and modifications
function doSomethingWithTime(ClockInterface $clock) {
    $now = $clock->now();
    $yesterday = $clock->now()->modify('-1 day');
    $nextMonth = $clock->now()->modify('+1 month');

    // (...)
}
```

### `DoctrineMigrationsSafeMigration`

Detects potentially unsafe database migration operations that could break backward compatibility in Doctrine migrations. Operations can be marked as safe using the `@safe-migration` annotation.

#### Examples

❌ **Invalid:**
```php
class UnsafeMigration extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // These operations may not be backward compatible
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE user CHANGE name email VARCHAR(255)');
        $this->addSql('ALTER TABLE user DROP COLUMN name');
        $this->addSql('DROP DATABASE test');
        $this->addSql('TRUNCATE TABLE user');
    }
}
```

✅ **Valid alternatives:**
```php
class SafeMigration extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Safe operations (no annotation needed)
        $this->addSql('ALTER TABLE user ADD COLUMN email VARCHAR(255)');
        $this->addSql('CREATE INDEX idx_user_email ON user (email)');
        $this->addSql('INSERT INTO user (name) VALUES (\'John\')');

        // Unsafe operations marked as intentionally safe
        /** @safe-migration */
        $this->addSql('DROP TABLE legacy_user');

        /** @safe-migration */
        $this->addSql('ALTER TABLE user CHANGE name full_name VARCHAR(255)');
    }
}
```

### `DoctrineMigrationsDescription`

Ensures that Doctrine migration classes provide meaningful descriptions by validating the return value of the `getDescription()` method against a configurable pattern.

#### Examples

❌ **Invalid:**
```php
class MyMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return ''; // Empty description
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD COLUMN email VARCHAR(255)');
    }
}
```

✅ **Valid:**
```php
class MyMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add email column to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD COLUMN email VARCHAR(255)');
    }
}
```

#### Configuration

By default, the rule accepts any non-empty string. You can customize the accepted pattern using the `services` key in your `phpstan.neon` configuration:

```neon
services:
    -
        class: NijiDigital\PhpStanRules\Rules\DoctrineMigrationsDescription
        arguments:
            acceptedPattern: '/^[A-Z][a-z0-9\s]+\.?$/'
        tags:
            - phpstan.rules.rule
```

## Troubleshooting

### PhpStorm doesn't provide any autocomplete on PhpStan classes

<details>
    Taken from [this blog](https://blog.bitexpert.de/blog/phpstorm_phpstan_wsl2_issue) :

    1. Copy the file `vendor/phpstan/phpstan/phpstan.phar` to a local folder of your choice, ie : `C:\php_include_path`.
    2. In PhpStorm, go to File | Settings | PHP and add your newly created folder to the include path.

</details>
