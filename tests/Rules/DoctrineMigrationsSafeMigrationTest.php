<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Tests\Rules;

use NijiDigital\PhpStanRules\Rules\DoctrineMigrationsSafeMigration;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DoctrineMigrationsSafeMigration>
 */
class DoctrineMigrationsSafeMigrationTest extends RuleTestCase
{
    public function testRule(): void
    {
        $tip = DoctrineMigrationsSafeMigration::TIP;

        $this->analyse([__DIR__ . '/data/DoctrineMigrationsSafeMigration/UnsafeMigration.php'], [
            [
                <<<MSG
                    A DROP TABLE operation may not be backward compatible.
                        💡 {$tip}
                    MSG,
                15,
            ],
            [
                <<<MSG
                    An ALTER TABLE CHANGE operation (such as changing the type of a column) may not be backward compatible.
                        💡 {$tip}
                    MSG,
                16,
            ],
            [
                <<<MSG
                    An ALTER TABLE DROP operation (such as dropping a column) may not be backward compatible.
                        💡 {$tip}
                    MSG,
                17,
            ],
            [
                <<<MSG
                    A DROP DATABASE operation may not be backward compatible.
                        💡 {$tip}
                    MSG,
                18,
            ],
            [
                <<<MSG
                    A TRUNCATE operation may not be backward compatible.
                        💡 {$tip}
                    MSG,
                19,
            ],
            [
                <<<MSG
                    A DROP TABLE operation may not be backward compatible.
                        💡 {$tip}
                    MSG,
                30,
            ],
            [
                <<<MSG
                    A TRUNCATE operation may not be backward compatible.
                        💡 {$tip}
                    MSG,
                31,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new DoctrineMigrationsSafeMigration(
            $this->getContainer()->getByType(RuleLevelHelper::class),
            $this->getContainer()->getByType(ReflectionProvider::class),
        );
    }
}
