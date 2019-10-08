<?php

declare(strict_types=1);

namespace Wiring\Traits;

use Wiring\Interfaces\ConfigInterface;

trait ConfigAwareTrait
{
    /**
     * @var ConfigInterface|null
     */
    protected $config;

    /**
     * Get the current config.
     *
     * @return ConfigInterface|null
     */
    public function getConfig(): ?ConfigInterface
    {
        return $this->config;
    }

    /**
     * Set the config implementation.
     *
     * @param ConfigInterface $config
     *
     * @return self
     */
    public function setConfig(ConfigInterface $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get settings properties.
     *
     * @throws \Exception
     *
     * @return ConfigInterface
     */
    public function config(): ConfigInterface
    {
        if (!$this->has(ConfigInterface::class)) {
            throw new \BadFunctionCallException('Config interface not implemented.');
        }

        return $this->get(ConfigInterface::class);
    }

    /**
     * Get language message properties.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function lang(string $key, $default = null)
    {
        return $this->config()->get('lang.' . $key, $default);
    }
}
