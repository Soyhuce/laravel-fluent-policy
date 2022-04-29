<?php declare(strict_types=1);

namespace Soyhuce\FluentPolicy;

use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Soyhuce\FluentPolicy\Exceptions\EarlyReturn;

abstract class FluentPolicy
{
    protected function allowWhen(bool $when, ?string $message = null, ?int $code = null): self
    {
        return $this->when($when, $this->allow($message, $code));
    }

    protected function denyWhen(bool $when, ?string $message = null, ?int $code = null): self
    {
        return $this->when($when, $this->deny($message, $code));
    }

    protected function when(bool $when, Response $response): self
    {
        throw_if($when, EarlyReturn::class, $response);

        return $this;
    }

    protected function allow(?string $message = null, ?int $code = null): Response
    {
        return Response::allow($message, $code);
    }

    protected function deny(?string $message = null, ?int $code = null): Response
    {
        return Response::deny($message, $code);
    }

    protected function otherwise(Response $response): Response
    {
        return $response;
    }

    protected function authorize(?Authenticatable $user, string $ability, mixed $parameters = []): self
    {
        app(Gate::class)->forUser($user)->authorize($ability, $parameters);

        return $this;
    }
}
