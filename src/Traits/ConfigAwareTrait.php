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
     * @return void
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Get settings properties.
     *
     * @param string $key
     * @throws \Exception
     *
     * @return mixed
     */
    public function config(string $key = '')
    {
        if (!$this->has(ConfigInterface::class)) {
            throw new \BadFunctionCallException('Config interface not implemented.');
        }

        $config = $this->get(ConfigInterface::class);

        if (!$config instanceof ConfigInterface) {
            throw new \UnexpectedValueException('Config interface not implemented.');
        }

        return empty($key) ? $config : $config->get($key);
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
        /** @var ConfigInterface $config */
        $config = $this->config();

        return $config->get('lang.' . $key, $default);
    }
}
