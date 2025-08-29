<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Psr\Clock\ClockInterface;

/**
 * @implements Rule<New_>
 */
class NoRelativeDatetime implements Rule
{
    private const TIP = 'Use dependency injection to get an instance of ' . ClockInterface::class;

    #[\Override]
    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @return RuleError[]
     */
    #[\Override]
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof New_) {
            return [];
        }

        if (!$node->class instanceof Name) {
            return [];
        }

        $className = $node->class->toString();
        if (!in_array($className, [\DateTime::class, \DateTimeImmutable::class], true)) {
            return [];
        }

        if ([] === $node->getArgs()) {
            return [
                RuleErrorBuilder::message(
                    sprintf(
                        'Usage of %s constructor without any argument is forbidden. Use %s::now().',
                        $className,
                        ClockInterface::class
                    )
                )
                    ->identifier('noRelativeDatetime.emptyConstructorCall')
                    ->tip(self::TIP)
                    ->build(),
            ];
        }

        $firstArg = $node->getArgs()[0]->value;
        if (!$firstArg instanceof String_) {
            return [];
        }

        $dateString = strtolower($firstArg->value);

        $relativeKeywords = [
            'now', 'today', 'tomorrow', 'yesterday',
            'next', 'last', 'first', 'second',
            'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
            'january', 'february', 'march', 'april', 'may', 'june', 'july',
            'august', 'september', 'october', 'november', 'december',
            'week', 'month', 'year', 'day',
        ];

        foreach ($relativeKeywords as $keyword) {
            if (str_contains($dateString, $keyword)) {
                return [
                    RuleErrorBuilder::message(
                        sprintf(
                            'Usage of relative date format ("%1$s") in %2$s is forbidden. Use %3$s::now()->modify("%1$s").',
                            $dateString,
                            $className,
                            ClockInterface::class
                        )
                    )
                        ->identifier('noRelativeDatetime.relativeDate')
                        ->tip(self::TIP)
                        ->build(),
                ];
            }
        }

        return [];
    }
}
