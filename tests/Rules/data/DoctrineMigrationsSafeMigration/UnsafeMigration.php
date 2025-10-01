<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Tests\Rules\data\DoctrineMigrationsSafeMigration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class UnsafeMigration extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Unsafe operations, not tagged as safe
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE user CHANGE name email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP COLUMN name');
        $this->addSql('DROP DATABASE test');
        $this->addSql('TRUNCATE TABLE user');

        // Unsafe operations, tagged as safe
        /** @safe-migration */
        $this->addSql('DROP TABLE user');
        /** @safe-migration */
        $this->addSql('ALTER TABLE user CHANGE name email VARCHAR(255) NOT NULL');
        /** @safe-migration */
        $this->addSql('ALTER TABLE user DROP COLUMN name');

        // Some unsafe operations again to make sure that the tag does not apply to them
        $this->addSql('DROP TABLE user');
        $this->addSql('TRUNCATE TABLE user');

        // Safe operations (do not impact backward compatibility and do not need to be tagged as safe)
        $this->addSql('ALTER TABLE user ADD COLUMN name VARCHAR(255) NOT NULL');
        $this->addSql('SELECT * FROM user');
        $this->addSql("INSERT INTO user (name) VALUES ('John')");
    }
}