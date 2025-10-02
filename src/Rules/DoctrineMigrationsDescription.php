<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Rules;

use Doctrine\Migrations\AbstractMigration;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Return_>
 */
class DoctrineMigrationsDescription implements Rule
{
    public const ERROR_IDENTIFIER = 'doctrineMigrations.invalidDescription';

    public const DEFAULT_ACCEPTED_PATTERN = '/.+/im';

    private readonly string $acceptedPattern;

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        ?string $acceptedPattern = self::DEFAULT_ACCEPTED_PATTERN,
    ) {
        $this->acceptedPattern = $acceptedPattern ?? self::DEFAULT_ACCEPTED_PATTERN;
    }

    #[\Override]
    public function getNodeType(): string
    {
        return Return_::class;
    }

    #[\Override]
    public function processNode(Node $node, Scope $scope): array
    {
        // Only handle scalar returned values
        if (!$node->expr instanceof String_) {
            return [];
        }

        // Make sure we're in a class
        $scopeMethod = $scope->getFunction();
        if (!$scopeMethod instanceof MethodReflection) {
            return [];
        }

        // Make sure we're in the `getDescription()` method of a migration class
        $classReflection = $scopeMethod->getDeclaringClass();
        if (!$classReflection->isSubclassOfClass($this->reflectionProvider->getClass(AbstractMigration::class)) || 'getDescription' !== $scopeMethod->getName()) {
            return [];
        }

        return preg_match($this->acceptedPattern, $node->expr->value) ? [] : [
            RuleErrorBuilder::message(sprintf('Doctrine migrations descriptions should respect the following pattern: %s', $this->acceptedPattern))
                ->identifier(self::ERROR_IDENTIFIER)
                ->build(),
        ];
    }
}
