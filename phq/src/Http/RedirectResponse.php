<?php

namespace PHQ\Http;

use GuzzleHttp\Psr7\Response;

class RedirectResponse extends Response
{

    /**
     * JsonResponse constructor.
     * @param string $path
     * @param array $headers
     */
    public function __construct(string $path = '/', array $headers = [])
    {
        parent::__construct(200, array_merge($headers, ['Location' => $path]));
    }
}
