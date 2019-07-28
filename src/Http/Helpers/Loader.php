<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

class Loader
{
    protected $path = [];
    protected $filetypes;

    /**
     * Loader constructor.
     *
     * @param array $filetypes
     */
    public function __construct(array $filetypes = ['php'])
    {
        $this->filetypes = $filetypes;
    }

    /**
     * Add paths to load.
     *
     * @param string $path
     */
    public function addPath(string $path)
    {
        $this->path[] = $path;
    }

    /**
     * Get files in an array for a single value through an
     * iterative process via callback function.
     *
     * @return array
     */
    public function load(): array
    {
        $scripts = [];

        foreach ($this->path as $path) {
            // Get files
            foreach ($this->filetypes as $filetype) {
                $scripts[] = glob($path . "/*.{$filetype}");
            }
        }

        return array_reduce($scripts, 'array_merge', []);
    }
}
