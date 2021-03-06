<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\CookieInterface;

trait CookieAwareTrait
{
    /**
     * @var CookieInterface|null
     */
    protected $cookie;

    /**
     * Get the current cookie.
     *
     * @return CookieInterface|null
     */
    public function getCookie(): ?CookieInterface
    {
        return $this->cookie;
    }

    /**
     * Set the cookie implementation.
     *
     * @param CookieInterface $cookie
     *
     * @return void
     */
    public function setCookie(CookieInterface $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Get on the client cookies.
     *
     * @throws \Exception
     *
     * @return CookieInterface
     */
    public function cookie(): CookieInterface
    {
        if (!$this->has(CookieInterface::class)) {
            throw new \BadFunctionCallException('Cookie interface not implemented.');
        }

        return $this->get(CookieInterface::class);
    }
}
