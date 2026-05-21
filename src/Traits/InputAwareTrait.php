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
        $body = $request->getBody()->getContents();
        $content = $body;

        if (isset($header[0])) {
            $type = (string) $header[0];
        }

        $type = strtolower($type);

        if ((str_contains($type, 'multipart/form-data')) ||
            (str_contains($type, 'application/x-www-form-urlencoded'))) {
            // Convert Multipart
            $content = $isArray ?
                $request->getParsedBody() :
                (object) $request->getParsedBody();
        }

        if (str_contains($type, 'application/json')) {
            // Convert JSON
            $content = json_decode($body, $isArray);
        }

        if (str_contains($type, 'application/xml')) {
            // Convert XML
            $previousUseInternalErrors = libxml_use_internal_errors(true);
            $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NONET);
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseInternalErrors);

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
