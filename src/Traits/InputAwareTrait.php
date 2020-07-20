<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait InputAwareTrait
{
    /**
     * Get input params.
     *
     * @param ServerRequestInterface $request
     * @param bool $isArray
     * @throws \Exception
     *
     * @return mixed
     */
    public function input(ServerRequestInterface $request, bool $isArray = false)
    {
        $header = $request->getHeader('content-type');
        $type = '';
        $content = $request->getBody()->getContents();

        if (isset($header[0])) {
            $type = (string) $header[0];
        }

        if ((strpos($type, 'multipart/form-data') !== false) ||
            (strpos($type, 'application/x-www-form-urlencoded') !== false)) {
            // Convert Multipart
            $content = $isArray ?
                $request->getParsedBody() :
                (object) $request->getParsedBody();
        }

        if (strpos($type, 'application/json') !== false) {
            // Convert JSON
            $content = json_decode($request->getBody()->getContents(), $isArray);
        }

        if (strpos($type, 'application/xml') !== false) {
            // Convert XML
            $xml = simplexml_load_string($request->getBody()->getContents());
            $content = json_decode((string) json_encode($xml), $isArray);
        }

        return $content;
    }

    /**
     * Get query params.
     *
     * @param ServerRequestInterface $request
     * @param bool $isArray
     * @throws \Exception
     *
     * @return mixed
     */
    public function query(ServerRequestInterface $request, bool $isArray = false)
    {
        return $isArray ? $request->getQueryParams() :
            (object) $request->getQueryParams();
    }
}
