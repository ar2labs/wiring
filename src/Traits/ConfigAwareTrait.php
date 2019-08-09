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
     * @throws Exception
     *
     * @return ConfigInterface
     */
    public function config(): ConfigInterface
    {
        if (!method_exists($this, 'has')) {
            throw new Exception('Container instance not found.');
        }

        if (!$this->has(ConfigInterface::class)) {
            throw new Exception('Config interface not defined.');
        }

        return $this->get(ConfigInterface::class);
    }

    /**
     * Get language message properties.
     *
     * @param $key
     * @return mixed
     */
    public function lang($key)
    {
        return $this->config('lang.' . $key);
    }
}
