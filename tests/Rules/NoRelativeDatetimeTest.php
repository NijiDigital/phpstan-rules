<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Tests\Rules;

use NijiDigital\PhpStanRules\Rules\NoRelativeDatetime;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoRelativeDatetime>
 */
class NoRelativeDatetimeTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new NoRelativeDatetime();
    }

    public function testRule(): void
    {
        $tip = NoRelativeDatetime::TIP;

        $this->analyse([__DIR__ . '/data/datetime.php'], [
            [
                <<<MSG
                    Usage of DateTime constructor without any argument is forbidden. Use Psr\Clock\ClockInterface::now().
                        💡 {$tip}
                    MSG,
                4,
            ],
            [
                <<<MSG
                    Usage of DateTimeImmutable constructor without any argument is forbidden. Use Psr\Clock\ClockInterface::now().
                        💡 {$tip}
                    MSG,
                5,
            ],
            [
                <<<MSG
                    Usage of relative date format ("yesterday") in DateTime is forbidden. Use Psr\Clock\ClockInterface::now()->modify("yesterday").
                        💡 {$tip}
                    MSG,
                6,
            ],
            [
                <<<MSG
                    Usage of relative date format ("yesterday") in DateTimeImmutable is forbidden. Use Psr\Clock\ClockInterface::now()->modify("yesterday").
                        💡 {$tip}
                    MSG,
                7,
            ],
            [
                <<<MSG
                    Usage of relative date format ("next month") in DateTime is forbidden. Use Psr\Clock\ClockInterface::now()->modify("next month").
                        💡 {$tip}
                    MSG,
                8,
            ],
            [
                <<<MSG
                    Usage of relative date format ("next month") in DateTimeImmutable is forbidden. Use Psr\Clock\ClockInterface::now()->modify("next month").
                        💡 {$tip}
                    MSG,
                9,
            ],
            [
                <<<MSG
                    Usage of relative date format ("+2 hours") in DateTime is forbidden. Use Psr\Clock\ClockInterface::now()->modify("+2 hours").
                        💡 {$tip}
                    MSG,
                10,
            ],
            [
                <<<MSG
                    Usage of relative date format ("+2 hours") in DateTimeImmutable is forbidden. Use Psr\Clock\ClockInterface::now()->modify("+2 hours").
                        💡 {$tip}
                    MSG,
                11,
            ],
        ]);
    }
}
