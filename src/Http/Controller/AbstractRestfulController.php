<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Wiring\Interfaces\RestfulControllerInterface;

abstract class AbstractRestfulController extends AbstractJsonController implements RestfulControllerInterface
{
    /**
     * List an existing resource.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface $response
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->getResponse();
        $data = $this->methodNotImplemented();

        return $this->json()->render($data)->to($response);
    }

    /**
     * Create an existing resource.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface $response
     */
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return $this->methodNotImplemented();
    }

    /**
     * Get an existing resource.
     *
     * @param ServerRequestInterface $request
     * @param array $args
     *
     * @return ResponseInterface $response
     */
    public function read(
        ServerRequestInterface $request,
        array $args
    ): ResponseInterface {
        return $this->methodNotImplemented();
    }

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
    ): ResponseInterface {
        return $this->methodNotImplemented();
    }

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
    ): ResponseInterface {
        return $this->methodNotImplemented();
    }

    /**
     * Get response data info.
     *
     * @param string $message
     * @param int $status
     * @param mixed|array $data
     *
     * @return mixed|array
     */
    public function info(
        string $message = 'Continue',
        int $status = 100,
        $data = []
    ) {
        return $this->data('info', $message, $data, $status);
    }

    /**
     * Get response data success.
     *
     * @param string $message
     * @param int $status
     * @param mixed|array $data
     *
     * @return mixed|array
     */
    public function success(
        string $message = 'OK',
        $data = [],
        int $status = 200
    ) {
        return $this->data('success', $message, $data, $status);
    }

    /**
     * Custom Redirect response.
     *
     * @param ResponseInterface $response
     * @param string $url
     * @param int|null $status
     * @return ResponseInterface
     */
    public function redirect(
        ResponseInterface $response,
        string $url,
        ?int $status = 307
    ): ResponseInterface {
        return $this->data('info', $response->getReasonPhrase(), [], $status);
    }

    /**
     * Get response data error.
     *
     * @param string $message
     * @param int $status
     * @param mixed|array $data
     *
     * @return mixed|array
     */
    public function error(
        string $message = 'Bad Request',
        int $status = 400,
        $data = []
    ) {
        return $this->data('error', $message, $data, $status);
    }

    /**
     * Get response data fail.
     *
     * @param string $message
     * @param int $status
     * @param mixed|array $data
     *
     * @return mixed|array
     */
    public function fail(
        string $message = 'Internal Server Error',
        int $status = 500,
        $data = []
    ) {
        return $this->data('fail', $message, $data, $status);
    }

    /**
     * Get response data.
     *
     * @param string $status
     * @param string $message
     * @param mixed|array $data
     * @param int|null $code
     *
     * @return mixed|array
     */
    public function data(
        string $status,
        string $message = 'OK',
        $data = [],
        ?int $code = 200
    ) {
        return [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Get Method Not Allowed.
     *
     * @return mixed|array
     */
    private function methodNotImplemented()
    {
        $data = $this->error('Method Not Implemented', 501);
        $response = $this->getResponse();

        return $this->json()->render($data)->to($response, $data['code']);
    }
}
