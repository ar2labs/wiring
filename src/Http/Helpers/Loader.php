<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

class Loader
{
    /** @var list<string> */
    protected $path = [];

    /** @var list<string> */
    protected $filetypes;

    /**
     * Loader constructor.
     *
    * @param list<string> $filetypes
     */
    public function __construct(array $filetypes = ['php'])
    {
        $this->filetypes = $filetypes;
    }

    /**
     * Add paths to load.
     *
     * @param string $path
     *
     * @return self
     */
    public function addPath(string $path)
    {
        $this->path[] = $path;

        return $this;
    }

    /**
     * Get files in an array for a single value through an
     * iterative process via callback function.
     *
     * @return list<string>
     */
    public function load(): array
    {
        $scripts = [];

        foreach ($this->path as $path) {
            // Get files
            foreach ($this->filetypes as $filetype) {
                $files = glob($path . "/*.{$filetype}");

                if (is_array($files)) {
                    $scripts = array_merge($scripts, $files);
                }
            }
        }

        return $scripts;
    }
}
