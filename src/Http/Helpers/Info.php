<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

class Info
{
    /**
     * Get custom PHP info.
     *
     * @return mixed
     */
    public function phpinfo()
    {
        // Start buffering
        ob_start();

        // Outputs information about PHP's configuration
        phpinfo();

        // Return the contents of the output buffer
        $content = ob_get_contents();

        // Stop buffering
        ob_end_clean();

        $appVersion = '2.0.0';

        if (defined('APP_VERSION')) {
            $version = constant('APP_VERSION');
            $appVersion = is_scalar($version) || $version === null ? (string) $version : $appVersion;
        }

        // Set custom version info
        $ver = sprintf(
            'PHP Version %s / Wiring Version %s',
            phpversion(),
            $appVersion
        );

        // Regex replacements
        $exp = [
            '%^.*<body>(.*)</body>.*$%ms' => '$1',
            '/<h1 class="p">(.*?)<\/h1>/i' => '<h1 class="p">' . $ver . '</h1>',
            '/<img[^>]+\>/i' => '<img src="./img/watermark.png" height="56">',
        ];

        // Perform a regular expression search and replace
        return preg_replace(
            array_keys($exp),
            array_values($exp),
            is_string($content) ? $content : ''
        );
    }
}
