<?php declare(strict_types=1);

namespace Soyhuce\FluentPolicy\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutDeprecationHandling();
    }
}
