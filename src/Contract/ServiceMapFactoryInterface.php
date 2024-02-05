<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Contract;

interface ServiceMapFactoryInterface
{
	public function create(): ServiceMapInterface;
}
