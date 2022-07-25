<?php declare(strict_types=1);

namespace Soyhuce\FluentPolicy\Phpstan;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\MethodTypeSpecifyingExtension;
use Soyhuce\FluentPolicy\FluentPolicy;
use UnexpectedValueException;
use function in_array;

class WhenMethodTypeSpecifyingExtension implements MethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private TypeSpecifier $typeSpecifier;

    public function getClass(): string
    {
        return FluentPolicy::class;
    }

    public function isMethodSupported(
        MethodReflection $methodReflection,
        MethodCall $node,
        TypeSpecifierContext $context,
    ): bool {
        return in_array(
            $methodReflection->getName(),
            ['denyWhen', 'denyWithStatusWhen', 'denyAsNotFoundWhen', 'allowWhen', 'when'],
            true
        );
    }

    public function specifyTypes(
        MethodReflection $methodReflection,
        MethodCall $node,
        Scope $scope,
        TypeSpecifierContext $context,
    ): SpecifiedTypes {
        if ($node->args[0] instanceof VariadicPlaceholder) {
            throw new UnexpectedValueException("Can't analyse this...");
        }

        return $this->typeSpecifier->specifyTypesInCondition(
            $scope,
            $node->args[0]->value,
            TypeSpecifierContext::createFalsey()
        );
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }
}
