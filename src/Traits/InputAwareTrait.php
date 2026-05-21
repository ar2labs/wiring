<?php

declare(strict_types=1);

namespace Wiring\Traits;

use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ServerRequestInterface;
use SimpleXMLElement;

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
            if (trim($body) === '') {
                return null;
            }

            try {
                $content = json_decode($body, $isArray, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new InvalidArgumentException('Invalid JSON request body: ' . $exception->getMessage(), 0, $exception);
            }
        }

        if (str_contains($type, 'application/xml')) {
            // Convert XML
            if (trim($body) === '') {
                return null;
            }

            $previousUseInternalErrors = libxml_use_internal_errors(true);
            try {
                $xml = simplexml_load_string($body, SimpleXMLElement::class, LIBXML_NONET);

                if ($xml === false) {
                    throw new InvalidArgumentException('Invalid XML request body.');
                }

                $json = json_encode($xml, JSON_THROW_ON_ERROR);
                $content = json_decode($json, $isArray, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                throw new InvalidArgumentException('Invalid XML request body.', 0, $exception);
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previousUseInternalErrors);
            }
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
