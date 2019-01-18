<?php

namespace App\Blog\Widgets;

use App\Blog\Repositories\IPostRepository;
use PHQ\Rendering\IRenderer;
use PHQ\Widgets\WidgetRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogWidget extends WidgetRenderer
{
    const POSITION = 10;

    /**
     * @var IPostRepository
     */
    private $repository;

    public function __construct(ServerRequestInterface $serverRequest, IRenderer $renderer, IPostRepository $repository)
    {
        parent::__construct($serverRequest, $renderer);
        $this->repository = $repository;
    }

    protected function getData()
    {
        return $this->repository->getLastPost();
    }

    protected function normalResponse($data): ResponseInterface
    {
        return $this->renderer->render('@blog/widgets/base', compact('data'));
    }
}
