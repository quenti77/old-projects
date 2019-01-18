<?php

namespace Tests\PHQ\Routing;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PHQ\Http\ServerRequest;
use PHQ\Routing\IRoute;
use PHQ\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FakeRouteController implements MiddlewareInterface
{

    /**
     * @var string $body
     */
    private $body;

    public function __construct(string $body = 'hello')
    {
        $this->body = $body;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, ?RequestHandlerInterface $handler = null): ResponseInterface
    {
        return new Response(200, [], $this->body);
    }
}

/**
 * Class RouterTest
 * @package Tests\PHQ\Routing
 */
class RouterTest extends TestCase
{

    /**
     * @var Router $router
     */
    private $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    /**
     * Test get is called and return value
     * @throws \Exception
     */
    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', new FakeRouteController('bonjour'), 'blog');

        $route = $this->router->match($request);

        $this->assertSame('blog', $route->getName());
        $this->assertSame('bonjour', $this->getBodyForRoute($route, $request));
    }

    /**
     * @param IRoute $route
     * @param ServerRequest $request
     * @return string
     */
    private function getBodyForRoute(IRoute $route, ServerRequest $request): string
    {
        return (string)$route->getCallback()
            ->process($request, null)
            ->getBody();
    }
}
