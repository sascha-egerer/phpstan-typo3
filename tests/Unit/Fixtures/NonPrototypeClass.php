<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Fixtures;

final class NonPrototypeClass
{
    /**
     * @param string|mixed|null $requiredString
     */
    public function __construct(string $requiredString) {}

}
