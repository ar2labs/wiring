<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait InputAwareTrait
{
    /**
     * Get container authentication instance.
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
        $content = (string) $header[0];

        if (strpos($content, 'multipart/form-data') !== false) {
            // Convert Multipart
            return $isArray ?
                $request->getParsedBody() :
                (object) $request->getParsedBody();
        }

        if (strpos($content, 'application/json') !== false) {
            // Convert JSON
            return json_decode($request->getBody()->getContents(), $isArray);
        }

        if (strpos($content, 'application/xml') !== false) {
            // Convert XML
            $xml = simplexml_load_string($request->getBody()->getContents());

            return json_decode((string) json_encode($xml), $isArray);
        }

        return $request->getBody()->getContents();
    }
}
