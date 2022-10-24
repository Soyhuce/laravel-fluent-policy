<?php declare(strict_types=1);

namespace Soyhuce\FluentPolicy\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutDeprecationHandling();
    }
}
