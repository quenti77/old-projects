<?php

namespace PHQ\Http;

use GuzzleHttp\Psr7\Response;

class JsonResponse extends Response
{
    /**
     * @var mixed
     */
    private $dataJson = null;

    /**
     * @return mixed
     */
    public function getDataJson()
    {
        return $this->dataJson;
    }

    /**
     * JsonResponse constructor.
     * @param $body
     * @param int $status
     */
    public function __construct($body, int $status = 200)
    {
        $version = '1.1';
        $reason = null;

        $this->dataJson = $body;
        $body = json_encode($body);

        parent::__construct($status, [
            'Content-Type' => 'application/json'
        ], $body, $version, $reason);
    }
}
