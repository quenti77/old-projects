<?php

namespace App\Blog\Actions;

use App\Blog\Entities\Post;
use App\Blog\Renderer\IndexRenderer;
use App\Blog\Repositories\IPostRepository;
use PHQ\Validations\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IndexAction implements MiddlewareInterface
{

    /**
     * @var IPostRepository $postRepository
     */
    private $postRepository;

    /**
     * @var IndexRenderer $renderer
     */
    private $renderer;

    public function __construct(IPostRepository $postRepository, IndexRenderer $renderer)
    {
        $this->postRepository = $postRepository;
        $this->renderer = $renderer;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $posts = $this->postRepository->getLastPost();
        return $this->renderer->send($posts);
    }
}
