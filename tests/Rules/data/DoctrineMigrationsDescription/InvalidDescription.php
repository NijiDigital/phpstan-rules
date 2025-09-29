<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Tests\Rules\data\DoctrineMigrationsDescription;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class InvalidDescription extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Test';
    }

    public function up(Schema $schema): void
    {
        // Noop
    }
}
