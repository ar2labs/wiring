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
     * @param mixed $obj
     *
     * @return self
     */
    public function log($obj)
    {
        $this->method(self::LOG, $obj);

        return $this;
    }

    /**
     * Write output to console browser.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function debug($obj)
    {
        $this->method(self::LOG, $obj);

        return $this;
    }

    /**
     * Write output to console browser in table format.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function table($obj)
    {
        $this->method('table', $obj);

        return $this;
    }

    /**
     * Write output to console browser in info logging method.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function info($obj)
    {
        $this->method('info', $obj);

        return $this;
    }

    /**
     * Write output to console browser in warning logging method.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function warn($obj)
    {
        $this->method('warn', $obj);

        return $this;
    }

    /**
     * Write output to console browser in error logging method.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function error($obj)
    {
        $this->method('error', $obj);

        return $this;
    }

    /**
     * Write output to console browser in stack trace.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function trace($obj)
    {
        $this->method('trace', $obj);

        return $this;
    }

    /**
     * Write output to console browser in a nice formatted way.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function dir($obj)
    {
        $this->method('dir', $obj);

        return $this;
    }

    /**
     * Write output to console browser in a DOM element’s markup.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function dirxml($obj)
    {
        $this->method('dirxml', $obj);

        return $this;
    }

    /**
     * Write output to console browser in an easy way to
     * run simple assertion tests.
     *
     * @param array<int, mixed> $args
     *
     * @return self
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

        return $this;
    }

    /**
     * Clear the console.
     *
     * @return self
     */
    public function clear()
    {
        unset($_SESSION[self::CONSOLE_LOG]);

        return $this;
    }

    /**
     * This method is used to count the number of times it has been invoked
     * with the same provided label.
     *
     * @param string|null $name
     *
     * @return self
     */
    public function count(?string $name = 'even')
    {
        $this->method('count', $name);

        return $this;
    }

    /**
     * Start a timer with this method.
     * Optionally the time can have a label.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function time($obj)
    {
        $this->method('time', $obj);

        return $this;
    }

    /**
     * Finish a timer with this method.
     * Optionally the time can have a label.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function timeend($obj)
    {
        $this->method('timeEnd', $obj);

        return $this;
    }

    /**
     * Use this method to group console messages together
     * with an optional label.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function group($obj)
    {
        $this->method('group', $obj);

        return $this;
    }

    /**
     * Use this method to end an group of the console messages.
     *
     * @param mixed $obj
     *
     * @return self
     */
    public function groupend($obj = null)
    {
        $this->method('groupEnd', $obj);

        return $this;
    }

    /**
     * Write output to console browser.
     *
     * @param string        $method
     * @param mixed $obj
     *
     * @return void
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
