<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

class Info
{
    /**
     * Get custom PHP info.
     *
     * @return string
     */
    public function phpinfo(): string
    {
        // Start buffering
        ob_start();

        // Outputs information about PHP's configuration
        phpinfo();

        // Return the contents of the output buffer
        $content = ob_get_contents();

        // Stop buffering
        ob_end_clean();

        // Set custom version info
        $ver = sprintf(
            'PHP Version %s / Wiring Version %s',
            phpversion(),
            'APP_VERSION'
        );

        // Regex replacements
        $exp = [
            '%^.*<body>(.*)</body>.*$%ms' => '$1',
            '/<h1 class="p">(.*?)<\/h1>/i' => '<h1 class="p">' . $ver . '</h1>',
            '/<img[^>]+\>/i' => '<img src="./img/watermark.png" height="56">',
        ];

        //  Perform a regular expression search and replace
        $replacements = preg_replace(
            array_keys($exp),
            array_values($exp),
            $content
        );

        // Print custom content
        return $replacements;
    }
}
