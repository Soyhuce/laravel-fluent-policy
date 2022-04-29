<?php declare(strict_types=1);

namespace Soyhuce\FluentPolicy\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;

class EarlyReturn extends AuthorizationException
{
    public function __construct(Response $response)
    {
        parent::__construct($response->message(), $response->code());
        $this->setResponse($response);
    }

    public function toResponse(): Response
    {
        return $this->response();
    }
}
