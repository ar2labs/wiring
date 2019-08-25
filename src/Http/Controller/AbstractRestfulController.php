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
        $response = $this->getResponse();
        $data = $this->methodNotImplemented();

        return $this->json()->render($data)->to($response, $data['code']);
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
        $response = $this->getResponse();
        $data = $this->methodNotImplemented();

        return $this->json()->render($data)->to($response, $data['code']);
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
        $response = $this->getResponse();
        $data = $this->methodNotImplemented();

        return $this->json()->render($data)->to($response, $data['code']);
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
        $response = $this->getResponse();
        $data = $this->methodNotImplemented();

        return $this->json()->render($data)->to($response, $data['code']);
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
        $data = $this->data('info', $message, $data, $status);

        return $data;
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
        $data = $this->data('success', $message, $data, $status);

        return $data;
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
        $data = $this->data('info', $response->getReasonPhrase(), [], $status);

        return $data;
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
        $data = $this->data('error', $message, $data, $status);

        return $data;
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
        $data = $this->data('fail', $message, $data, $status);

        return $data;
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
        $result = [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        return $result;
    }

    /**
     * Get Method Not Allowed.
     *
     * @return mixed|array
     */
    private function methodNotImplemented()
    {
        return $this->error('Method Not Implemented', 501);
    }
}
