<?php

declare(strict_types=1);

namespace Wiring\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RestfulControllerInterface
{
    /**
     * List an existing resource.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface     $response
     */
    public function index(ServerRequestInterface $request): ResponseInterface;

    /**
     * Create an existing resource.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface     $response
     */
    public function create(ServerRequestInterface $request);

    /**
     * Get an existing resource.
     *
     * @param ServerRequestInterface $request
     * @param array                  $args
     *
     * @return ResponseInterface $response
     */
    public function read(
        ServerRequestInterface $request,
        array $args
    ): ResponseInterface;

    /**
     * Update an existing resource.
     *
     * @param ServerRequestInterface $request
     * @param array                  $args
     *
     * @return ResponseInterface $response
     */
    public function update(
        ServerRequestInterface $request,
        array $args
    ): ResponseInterface;

    /**
     * Delete an existing resource.
     *
     * @param ServerRequestInterface $request
     * @param array                  $args
     *
     * @return ResponseInterface $response
     */
    public function delete(
        ServerRequestInterface $request,
        array $args
    ): ResponseInterface;
}
