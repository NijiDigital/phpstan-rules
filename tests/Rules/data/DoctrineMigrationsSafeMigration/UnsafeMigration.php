<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Tests\Rules\data\DoctrineMigrationsSafeMigration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class UnsafeMigration extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Unsafe operations, not ignored
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE user CHANGE name email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP COLUMN name');
        $this->addSql('DROP DATABASE test');
        $this->addSql('TRUNCATE TABLE user');

        // Unsafe operations, ignored
        /* @phpstan-ignore doctrineMigrations.unsafeMigration */
        $this->addSql('DROP TABLE user');
        /* @phpstan-ignore doctrineMigrations.unsafeMigration */
        $this->addSql('ALTER TABLE user CHANGE name email VARCHAR(255) NOT NULL');
        /* @phpstan-ignore doctrineMigrations.unsafeMigration */
        $this->addSql('ALTER TABLE user DROP COLUMN name');

        // Some unsafe operations again to make sure that the ignore tags do not apply to them
        $this->addSql('DROP TABLE user');
        $this->addSql('TRUNCATE TABLE user');

        // Safe operations (do not impact backward compatibility and do not need to be ignored)
        $this->addSql('ALTER TABLE user ADD COLUMN name VARCHAR(255) NOT NULL');
        $this->addSql('SELECT * FROM user');
        $this->addSql("INSERT INTO user (name) VALUES ('John')");
    }
}
