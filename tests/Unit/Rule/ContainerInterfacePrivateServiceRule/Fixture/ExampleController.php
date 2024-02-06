<?php declare(strict_types = 1);

namespace Unit\Rule\ContainerInterfacePrivateServiceRule\Fixture;

use Psr\Container\ContainerInterface;

final class ExampleController
{

	private ContainerInterface $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function action(): void
	{
		$privateService = $this->container->get('private');
		$publicService = $this->container->get('public');
		$nonExistingService = $this->container->get('non-existing-service');
		unset($nonExistingService, $privateService, $publicService);
	}

}
