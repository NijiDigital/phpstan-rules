<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Tests\Rules\data\DoctrineMigrationsDescription;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class ValidDescription extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[JIRA-1234] Test';
    }

    public function up(Schema $schema): void
    {
        // Noop
    }
}
