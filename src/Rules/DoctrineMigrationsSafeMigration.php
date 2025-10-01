<?php

declare(strict_types=1);

namespace NijiDigital\PhpStanRules\Rules;

use Doctrine\Migrations\AbstractMigration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;

/**
 * @implements Rule<MethodCall>
 */
class DoctrineMigrationsSafeMigration implements Rule
{
    public const ERROR_IDENTIFIER = 'doctrineMigrations.unsafeMigration';

    public const TIP = 'Avoid operations that are not backward compatibles or mark them as being safe using /** %s */';

    public const DEFAULT_SAFE_MIGRATION_TAG = '@safe-migration';

    public const DEFAULT_BLACKLISTED_QUERIES = [
        '/ALTER\s+TABLE\s+.+\s+CHANGE\s+/im' => 'An ALTER TABLE CHANGE operation (such as changing the type of a column) may not be backward compatible.',
        '/ALTER\s+TABLE\s+.+\s+DROP\s+(?!INDEX|FOREIGN KEY)/im' => 'An ALTER TABLE DROP operation (such as dropping a column) may not be backward compatible.',
        '/DROP\s+TABLE\s+/im' => 'A DROP TABLE operation may not be backward compatible.',
        '/DROP\s+DATABASE\s+/im' => 'A DROP DATABASE operation may not be backward compatible.',
        '/TRUNCATE\s+/im' => 'A TRUNCATE operation may not be backward compatible.',
    ];

    /** @var string[] */
    private readonly array $blacklistedQueries;

    private readonly string $safeMigrationTag;

    /**
     * @param string[]|null $blacklistedQueries
     */
    public function __construct(
        private readonly RuleLevelHelper $ruleLevelHelper,
        private readonly Lexer $phpDocLexer,
        private readonly PhpDocParser $phpDocParser,
        ?array $blacklistedQueries = self::DEFAULT_BLACKLISTED_QUERIES,
        ?string $safeMigrationTag = self::DEFAULT_SAFE_MIGRATION_TAG
    ) {
        $this->blacklistedQueries = $blacklistedQueries ?? self::DEFAULT_BLACKLISTED_QUERIES;
        $this->safeMigrationTag = $safeMigrationTag ?? self::DEFAULT_SAFE_MIGRATION_TAG;
    }

    #[\Override]
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    #[\Override]
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Identifier) {
            return [];
        }

        // Make sure we're in a class
        $scopeMethod = $scope->getFunction();
        if (!$scopeMethod instanceof MethodReflection) {
            return [];
        }

        // Make sure we're in the `up()` method of a migration class
        $classReflection = $scopeMethod->getDeclaringClass();
        if (!$classReflection->isSubclassOf(AbstractMigration::class) || 'up' !== $scopeMethod->getName()) {
            return [];
        }

        // Only apply the check to `addSql` calls
        $name = $node->name->name;
        if ('addSql' !== $name) {
            return [];
        }

        // Retrieve type information about the node
        $foundTypeResult = $this->ruleLevelHelper->findTypeToCheck(
            $scope,
            $node->var,
            'Call to method addSql on an unknown class %%s.',
            static fn(Type $type): bool => $type->canCallMethods()->yes() && $type->hasMethod($name)->yes()
        );

        $type = $foundTypeResult->getType();
        // If we could not retrieve the type abort the verification
        // (it will be done by other PHPStan rules anyway).
        if ($type instanceof ErrorType) {
            return [];
        }

        // Retrieve information about the called method
        $extendedMethodReflection = $type->getMethod($name, $scope);
        $methodDeclaringClass = $extendedMethodReflection->getDeclaringClass();

        // Check if the called method comes from the AbstractMigration class
        if (AbstractMigration::class === $methodDeclaringClass->getName()) {
            $parameters = $node->args;
            if (
                \count($parameters) >= 1 &&
                ($parameters[0] instanceof Arg) &&
                ($parameterType = $scope->getType($parameters[0]->value)) instanceof ConstantStringType &&
                ($blacklistedQueryMessage = $this->getBlacklistedQueryMessage($node, $parameterType->getValue()))) {
                return [
                    RuleErrorBuilder::message($blacklistedQueryMessage)
                        ->identifier(self::ERROR_IDENTIFIER)
                        ->tip(sprintf(self::TIP, $this->safeMigrationTag))
                        ->build(),
                ];
            }
        }

        return [];
    }

    private function getBlacklistedQueryMessage(Node $node, string $sqlQuery): ?string
    {
        if ($this->isTaggedAsSafeMigration($node)) {
            return null;
        }

        foreach ($this->blacklistedQueries as $blacklistedQuery => $message) {
            if (preg_match($blacklistedQuery, $sqlQuery)) {
                return $message;
            }
        }

        return null;
    }

    private function isTaggedAsSafeMigration(Node $node): bool
    {
        $docComment = $node->getDocComment();
        if (null === $docComment) {
            return false;
        }

        $phpDocString = $docComment->getText();
        $phpDocTokens = new TokenIterator($this->phpDocLexer->tokenize($phpDocString));
        $phpDocNode = $this->phpDocParser->parse($phpDocTokens);

        foreach ($phpDocNode->getTags() as $phpDocTagNode) {
            if ($this->safeMigrationTag === $phpDocTagNode->name) {
                return true;
            }
        }

        return false;
    }
}
