<?php declare(strict_types=1);

use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Soyhuce\FluentPolicy\FluentPolicy;
use Soyhuce\FluentPolicy\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toBeAllowed', function () {
    Gate::define('test', fn (?Authenticatable $user = null) => $this->value->run());

    expect(Gate::allows('test'))->toBe(true);

    return $this;
});

function inspect(FluentPolicy $policy): Response
{
    Gate::define('test', fn (?Authenticatable $user = null) => $policy->run());

    return Gate::inspect('test');
}
