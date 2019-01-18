<?php

namespace App\Blog\Actions;

use App\Blog\Entities\Post;
use App\Blog\Repositories\IPostRepository;
use GuzzleHttp\Psr7\Response;
use PHQ\Validations\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StoreAction implements MiddlewareInterface
{

    /**
     * @var IPostRepository $postRepository
     */
    private $postRepository;

    public function __construct(IPostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
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
        $validator = new Validator([
            'name' => 'required|min:3|max:30',
            'content' => 'required|min:5'
        ]);

        if (!$validator->validate($request)) {
            errors($validator->getErrors());
            return redirect('/blog');
        }

        flash('info', $validator['name']);

            $post = new Post([
            'name' => $validator['name'],
            'slug' => slugify($validator['name']),
            'content' => $validator['content']
            ]);

        $this->postRepository->save($post);

        flash('success', 'Votre article à bien été ajouté');
        return redirect('/blog');
    }
}
