<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Contract;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition;

interface ServiceMap
{

	/**
	 * @return ServiceDefinition[]
	 */
	public function getServiceDefinitions(): array;

	public function getServiceDefinitionById(string $id): ?ServiceDefinition;

	public function getServiceIdFromNode(Expr $node, Scope $scope): ?string;

}
