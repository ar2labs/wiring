<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

class Console
{
    // Define constants
    const LOG = 'log';
    const CONSOLE_LOG = 'console.log';

    /**
     * Write output to console browser.
     *
     * @var object|string $obj
     */
    public function log($obj)
    {
        $this->method(self::LOG, $obj);
    }

    /**
     * Write output to console browser.
     *
     * @var object|string $obj
     */
    public function debug($obj)
    {
        $this->method(self::LOG, $obj);
    }

    /**
     * Write output to console browser in table format.
     *
     * @var object|string $obj
     */
    public function table($obj)
    {
        $this->method('table', $obj);
    }

    /**
     * Write output to console browser in info logging method.
     *
     * @var object|string $obj
     */
    public function info($obj)
    {
        $this->method('info', $obj);
    }

    /**
     * Write output to console browser in warning logging method.
     *
     * @var object|string $obj
     */
    public function warn($obj)
    {
        $this->method('warn', $obj);
    }

    /**
     * Write output to console browser in error logging method.
     *
     * @var object|string $obj
     */
    public function error($obj)
    {
        $this->method('error', $obj);
    }

    /**
     * Write output to console browser in stack trace.
     *
     * @var object|string $obj
     */
    public function trace($obj)
    {
        $this->method('trace', $obj);
    }

    /**
     * Write output to console browser in a nice formatted way.
     *
     * @var object|string $obj
     */
    public function dir($obj)
    {
        $this->method('dir', $obj);
    }

    /**
     * Write output to console browser in a DOM element’s markup.
     *
     * @var object|string $obj
     */
    public function dirxml($obj)
    {
        $this->method('dirxml', $obj);
    }

    /**
     * Write output to console browser in an easy way to
     * run simple assertion tests.
     *
     * @var object[array]|string[array] $args
     */
    public function assert(...$args)
    {
        // Start buffering
        ob_start();

        // Aux array
        $data = [];

        // Convert elements array
        foreach ($args as $key => $arg) {
            // Check first argument
            if (($key == 0) && (is_bool($arg))) {
                array_push($data, (int) $arg);
            } // String args...
            else {
                array_push($data, "'$arg'");
            }
        }

        // Join array elements with a string delimiter
        $args = implode(',', $data);

        // Echo to output
        echo "console.assert($args);";

        // Return the contents of the output buffer
        $output = ob_get_contents();

        // Store output in new array with zero index
        $_SESSION[self::CONSOLE_LOG][] = $output;

        // Stop buffering
        ob_end_clean();
    }

    /**
     * Clear the console.
     */
    public function clear()
    {
        unset($_SESSION[self::CONSOLE_LOG]);
    }

    /**
     * This method is used to count the number of times it has been invoked
     * with the same provided label.
     *
     * @var string|null $obj
     */
    public function count(?string $name = 'even')
    {
        $this->method('count', $name);
    }

    /**
     * Start a timer with this method.
     * Optionally the time can have a label.
     *
     * @var object|string $obj
     */
    public function time($obj)
    {
        $this->method('time', $obj);
    }

    /**
     * Finish a timer with this method.
     * Optionally the time can have a label.
     *
     * @var object|string $obj
     */
    public function timeend($obj)
    {
        $this->method('timeEnd', $obj);
    }

    /**
     * Use this method to group console messages together
     * with an optional label.
     *
     * @var object|string $obj
     */
    public function group($obj)
    {
        $this->method('group', $obj);
    }

    /**
     * Use this method to end an group of the console messages.
     *
     * @var object|string $obj
     */
    public function groupend($obj = null)
    {
        $this->method('groupEnd', $obj);
    }

    /**
     * Write output to console browser.
     *
     * @var string        $method
     * @var object|string $obj
     */
    private function method(string $method, $obj)
    {
        // Start buffering
        ob_start();

        if (is_object($obj)) { // Check is an object
            $js = 'var JSONObject = ' . json_encode($obj) . ";\n"
                . 'var JSONString = JSON.stringify(JSONObject);'
                . 'var JSObject = JSON.parse(JSONString); '
                . "console.$method(JSObject);";
        } elseif (is_array($obj) || $method == 'dirxml') { // Check is an array
            $js = 'var data = ' . json_encode($obj) . '; '
                . "console.$method(data);";
        } elseif ($method == self::LOG) { // Check is set
            $js = "var data = '" . $obj . "'; "
                . "console.$method(data);";
        } else {  // Method is empty
            $js = "console.$method();";
        }

        echo $js;

        // Check is not empty
        if (!empty($obj)) {
            // Return the contents of the output buffer
            $output = ob_get_contents();

            // Store output in new array with zero index
            $_SESSION[self::CONSOLE_LOG][] = $output;
        }

        // Stop buffering
        ob_end_clean();
    }
}
