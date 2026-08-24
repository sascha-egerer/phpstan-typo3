<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Rules\RestrictedUsage\RestrictedFunctionUsageExtension;
use PHPStan\Rules\RestrictedUsage\RestrictedUsage;
use SaschaEgerer\PhpstanTypo3\Service\SystemConfigurationProvider;

class AllowedPhpDisableFunctionsUsageExtension implements RestrictedFunctionUsageExtension
{
    /** @var list<lowercase-string>|null */
    private ?array $allowedPhpDisableFunctions = null;

    public function __construct(private readonly SystemConfigurationProvider $systemConfigurationProvider) {}

    public function isRestrictedFunctionUsage(FunctionReflection $functionReflection, Scope $scope): ?RestrictedUsage
    {
        if (!in_array(strtolower($functionReflection->getName()), $this->getAllowedPhpDisableFunctions(), true)) {
            return null;
        }

        return RestrictedUsage::create(
            sprintf(
                'Function %s() is listed in TYPO3_CONF_VARS/SYS/allowedPhpDisableFunctions and might be disabled via the PHP setting "disable_functions" at runtime.',
                $functionReflection->getName()
            ),
            'phpstanTypo3.allowedPhpDisableFunctions'
        );
    }

    /**
     * @return list<lowercase-string>
     */
    private function getAllowedPhpDisableFunctions(): array
    {
        return $this->allowedPhpDisableFunctions ??= array_map(
            strtolower(...),
            $this->systemConfigurationProvider->getAllowedPhpDisableFunctions()
        );
    }

}
