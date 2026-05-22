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
        $type = $this->getContentType($request);
        $body = $request->getBody()->getContents();
        $content = $body;

        if ((str_contains($type, 'multipart/form-data')) ||
            (str_contains($type, 'application/x-www-form-urlencoded'))) {
            $content = $isArray ? $request->getParsedBody() : (object) $request->getParsedBody();
        } elseif (str_contains($type, 'application/json')) {
            $content = $this->parseJsonBody($body, $isArray);
        } elseif (str_contains($type, 'application/xml')) {
            $content = $this->parseXmlBody($body, $isArray);
        }

        return $content;
    }

    private function getContentType(ServerRequestInterface $request): string
    {
        $header = $request->getHeader('content-type');

        if (isset($header[0])) {
            return strtolower((string) $header[0]);
        }

        return strtolower($request->getHeaderLine('content-type'));
    }

    /**
     * @return mixed
     */
    private function parseJsonBody(string $body, bool $isArray)
    {
        if (trim($body) === '') {
            return null;
        }

        try {
            return json_decode($body, $isArray, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Invalid JSON request body: ' . $exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @return mixed
     */
    private function parseXmlBody(string $body, bool $isArray)
    {
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

            return json_decode($json, $isArray, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Invalid XML request body.', 0, $exception);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseInternalErrors);
        }
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
