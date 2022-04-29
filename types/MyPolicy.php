<?php

use Illuminate\Auth\Access\Response;
use Soyhuce\FluentPolicy\FluentPolicy;

class MyPolicy extends FluentPolicy
{
    public function simple(string $value): Response
    {
        return $this->allowWhen(strlen($value) < 5)
            ->denyWhen(strlen($value) < 10)
            ->allow();
    }

    public function earlyReturnIsUnderstood(?string $value): Response
    {
        $this->denyWhen($value === null);

        return $this
            ->allowWhen(strlen($value) < 10)
            ->deny();
    }

    public function earlyReturnIsUnderstoodWithWhen(?string $value): Response
    {
        $this->when($value === null, $this->deny());

        return $this
            ->when(strlen($value) < 10, $this->allow())
            ->deny();
    }

    // This syntax is not yet understood by phpstan
    // See https://github.com/phpstan/phpstan/discussions/5459
    public function fluentEarlyReturnIsUnderstoodWithWhen(?string $value): Response
    {
        return $this
            ->denyWhen($value === null)
            ->allowWhen(strlen($value) < 10)
            ->deny();
    }

    public function fluentEarlyReturnIsUnderstood(?string $value): Response
    {
        return $this
            ->when($value === null, $this->deny())
            ->when(strlen($value) < 10, $this->allow())
            ->deny();
    }
}
