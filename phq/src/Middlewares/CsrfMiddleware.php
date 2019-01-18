<?php

namespace PHQ\Middlewares;

use Faker\Factory;
use PHQ\Exceptions\CsrfTokenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws CsrfTokenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== 'GET') {
            $token = $request->getParsedBody()['__csrf'] ?? null;
            $tokenSession = session()->offsetGet('__csrf');

            if ($token === null || $tokenSession !== $token) {
                throw new CsrfTokenException('Token CSRF manquant ou invalid');
            }
        }

        $faker = Factory::create();
        session()->offsetSet('__csrf', $faker->uuid);

        return $handler->handle($request);
    }
}
