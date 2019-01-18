<?php

use PHQ\Http\ServerRequest;
use PHQ\Rendering\IRenderer;
use PHQ\Rendering\TwigRendererFactory;
use PHQ\Routing\IRouter;
use PHQ\Routing\Router;
use PHQ\Validations\Validators\ConfirmedValidator;
use PHQ\Validations\Validators\DateTimeValidator;
use PHQ\Validations\Validators\MaxValidator;
use PHQ\Validations\Validators\MinValidator;
use PHQ\Validations\Validators\RequiredValidator;
use Psr\Http\Message\ServerRequestInterface;

return [
    IRouter::class => DI\object(Router::class),

    'view.path' => ROOT.'/app/views',
    IRenderer::class => DI\factory(TwigRendererFactory::class),

    ServerRequestInterface::class => function () {
        return ServerRequest::fromGlobals();
    },


    'required' => DI\object(RequiredValidator::class),
    'min' => DI\object(MinValidator::class),
    'max' => DI\object(MaxValidator::class),
    'confirmed' => DI\object(ConfirmedValidator::class),
    'datetime' => DI\object(DateTimeValidator::class)
];
