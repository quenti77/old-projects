<?php

use GuzzleHttp\Psr7\Response;
use PHQ\App;
use PHQ\Http\JsonResponse;
use PHQ\Http\RedirectResponse;
use PHQ\Utils\Session;

if (!function_exists('app')) {

    /**
     * @return App|null
     * @throws Exception
     */
    function app(): App
    {
        static $app = null;

        if ($app === null) {
            $app = new App(ROOT . '/bin/configs');
        }

        return $app;
    }
}

if (!function_exists('response')) {

    /**
     * @param $body
     * @param int $status
     * @return Response
     */
    function response($body, int $status = 200): Response
    {
        return new Response($status, [], $body);
    }
}

if (!function_exists('json')) {

    /**
     * @param $body
     * @param int $status
     * @return JsonResponse
     */
    function json($body, int $status = 200): JsonResponse
    {
        return new JsonResponse($body, $status);
    }
}

if (!function_exists('redirect')) {

    /**
     * @param string $path
     * @param array $headers
     * @return RedirectResponse
     */
    function redirect(string $path = '/', array $headers = []): RedirectResponse
    {
        return new RedirectResponse($path, $headers);
    }
}

if (!function_exists('slug')) {

    /**
     * @param $text
     * @return null|string|string[]
     */
    function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

if (!function_exists('session')) {

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    function session(): Session
    {
        return app()->getContainer()->get(Session::class);
    }

}

if (!function_exists('flash')) {

    /**
     * @param string $type
     * @param string $content
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    function flash(string $type, string $content): void
    {
        $session = session();
        $flashes = $session['__flash'] ?? null;
        if ($flashes === null) {
            $flashes = $session['__flash'] = [];
        }

        $flashByType = $flashes[$type] ?? null;
        if ($flashByType === null) {
            $flashes[$type] = [$content];
        } else {
            $flashes[$type][] = $content;
        }

        $session['__flash'] = $flashes;
    }
}

if (!function_exists('errors')) {

    /**
     * @param array $errors
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    function errors(array $errors): void
    {
        session()->offsetSet('__errors', $errors);
    }
}
