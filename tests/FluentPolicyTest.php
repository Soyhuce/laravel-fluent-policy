<?php declare(strict_types=1);

use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Gate;
use Soyhuce\FluentPolicy\Exceptions\EarlyReturn;
use Soyhuce\FluentPolicy\FluentPolicy;

it('allows the action', function (FluentPolicy $policy): void {
    expect($policy)->toBeAllowed();
})->with([
    'silly' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->allow();
        }
    },
    'fallback' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyWhen(false)->allow();
        }
    },
    'simple' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->allowWhen(true)->deny();
        }
    },
    'first result' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyWhen(false)->allowWhen(true)->denyWhen(true)->deny();
        }
    },
    'unneeded tests are not executed' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this
                ->allowWhen(false)
                ->allowWhen(true)
                ->denyWhen(throw new Exception('Should not be called'))
                ->deny();
        }
    },
    'using another authorization' => new class() extends FluentPolicy {
        public function run(): Response
        {
            Gate::define('other authorization', fn (?Authorizable $user = null) => Response::allow());

            return $this->authorize(null, 'other authorization')->allow();
        }
    },
    'using another authorization with chain' => new class() extends FluentPolicy {
        public function run(): Response
        {
            Gate::define('other authorization', fn (?Authorizable $user = null) => Response::allow());

            return $this->authorize(null, 'other authorization')->allowWhen(true)->deny();
        }
    },
]);

it('denies the action', function (FluentPolicy $policy): void {
    expect($policy)->not->toBeAllowed();
})->with([
    'silly' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->deny();
        }
    },
    'fallback' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->allowWhen(false)->deny();
        }
    },
    'simple' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyWhen(true)->allow();
        }
    },
    'first result' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->allowWhen(false)->denyWhen(true)->allowWhen(true)->allow();
        }
    },
    'denied from another authorization' => new class() extends FluentPolicy {
        public function run(): Response
        {
            Gate::define('other authorization', fn (?Authorizable $user = null) => Response::deny());

            return $this->authorize(null, 'other authorization')->allow();
        }
    },
]);

it('denies the action with custom status', function (FluentPolicy $policy): void {
    expect(inspect($policy))
        ->allowed()->toBeFalse()
        ->status()->toBe(405);
})->with([
    'silly' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyWithStatus(405);
        }
    },
    'fallback' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->allowWhen(false)->denyWithStatus(405);
        }
    },
    'simple' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyWithStatusWhen(true, 405)->allow();
        }
    },
]);

it('denies the action as not found', function (FluentPolicy $policy): void {
    expect(inspect($policy))
        ->allowed()->toBeFalse()
        ->status()->toBe(404);
})->with([
    'silly' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyAsNotFound();
        }
    },
    'fallback' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->allowWhen(false)->denyAsNotFound();
        }
    },
    'simple' => new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyAsNotFoundWhen(true)->allow();
        }
    },
]);

it('customises the response', function (): void {
    expect(inspect(
        new class() extends FluentPolicy {
            public function run(): Response
            {
                return $this->denyWhen(true, 'The message', 418)->allow();
            }
        }
    ))
        ->message()->toBe('The message')
        ->code()->toBe(418);
});

it('gives the message to EarlyReturn exception', function (): void {
    $policy = new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->denyWhen(true, 'The message', 418)->allow();
        }
    };
    expect(fn () => $policy->run())
        ->toThrow(EarlyReturn::class, 'The message');
});

it('keeps original authorisation message', function (): void {
    Gate::define(
        'other authorization',
        fn (?Authorizable $user = null) => Response::deny('The message', 418)
    );

    $policy = new class() extends FluentPolicy {
        public function run(): Response
        {
            return $this->authorize(null, 'other authorization')->allow();
        }
    };

    expect(inspect($policy))
        ->message()->toBe('The message')
        ->code()->toBe(418);
});
