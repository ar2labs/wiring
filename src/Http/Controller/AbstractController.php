<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Interfaces\ControllerInterface;
use Wiring\Interfaces\ResponseAwareInterface;
use Wiring\Strategy\AbstractStrategy;
use Wiring\Traits\ContainerAwareTrait;
use Wiring\Traits\DatabaseAwareTrait;
use Wiring\Traits\ResponseAwareTrait;

abstract class AbstractController extends AbstractStrategy implements
    ContainerAwareInterface,
    ControllerInterface,
    ResponseAwareInterface
{
    // NOTE: Use Aware Traits for get instance of the other componentes.
    use ContainerAwareTrait;
    use ResponseAwareTrait;
    use DatabaseAwareTrait;

    /**
     * Redirect response.
     *
     * This method prepares the response object to return an
     * HTTP Redirect response to the client.
     *
     * @param ResponseInterface
     * @param string $url
     * @param int|null $status
     *
     * @return ResponseInterface $request
     */
    public function redirect(
        ResponseInterface $response,
        string $url,
        int $status = 307
    ) {
        $responseWithRedirect = $response->withHeader('Location', $url);

        if (!is_null($status)) {
            return $responseWithRedirect->withStatus($status);
        }

        return $responseWithRedirect;
    }
}
