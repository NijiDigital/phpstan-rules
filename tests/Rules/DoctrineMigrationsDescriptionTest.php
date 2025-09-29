<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Tests\Rules;

use NijiDigital\PhpStanRules\Rules\DoctrineMigrationsDescription;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DoctrineMigrationsDescription>
 */
class DoctrineMigrationsDescriptionTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/DoctrineMigrationsDescription/ValidDescription.php'], []);
        $this->analyse([__DIR__ . '/data/DoctrineMigrationsDescription/InvalidDescription.php'], [
            ['Doctrine migrations descriptions should respect the following pattern: /\\[JIRA-[0-9]+\\] .+/im', 14],
        ]);
    }

    protected function getRule(): Rule
    {
        return new DoctrineMigrationsDescription(
            '/\\[JIRA-[0-9]+\\] .+/im'
        );
    }
}
