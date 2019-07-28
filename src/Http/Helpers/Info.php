<?php

namespace Wiring\Http\Helpers;

class Info
{
    // Custom PHP info
    public function phpinfo()
    {
        // Start buffering
        ob_start();

        // Outputs information about PHP's configuration
        phpinfo();

        // Set custom version info
        $version = sprintf("PHP Version %s / Wiring Version %s", phpversion(), APP_VERSION);

        // Return the contents of the output buffer
        $content = ob_get_contents();

        // Stop buffering
        ob_end_clean();

        // Regex replacements
        $exp = [
            '%^.*<body>(.*)</body>.*$%ms' => '$1',
            '/<h1 class="p">(.*?)<\/h1>/i' => '<h1 class="p">' . $version . '</h1>',
            '/<img[^>]+\>/i' => '<img src="./img/php-watermark.png" height="56">'
        ];

        //  Perform a regular expression search and replace
        $replacements = preg_replace(array_keys($exp), array_values($exp), $content);

        // Print custom content
        return $replacements;
    }
}
