<?php

declare(strict_types=1);

namespace Wiring\Http\Helpers;

class Console
{
    /**
     * Write output to console browser.
     */
    public function log($obj = "")
    {
        $this->method($obj, 'log');
    }

    /**
     * Write output to console browser.
     */
    public function debug($obj)
    {
        $this->method($obj, 'log');
    }

    /**
     * Write output to console browser in table format.
     */
    public function table($obj)
    {
        $this->method($obj, 'table');
    }

    /**
     * Write output to console browser in info logging method.
     */
    public function info($obj)
    {
        $this->method($obj, 'info');
    }

    /**
     * Write output to console browser in warning logging method.
     */
    public function warn($obj)
    {
        $this->method($obj, 'warn');
    }

    /**
     * Write output to console browser in error logging method.
     */
    public function error($obj)
    {
        $this->method($obj, 'error');
    }

    /**
     * Write output to console browser in stack trace.
     */
    public function trace($obj)
    {
        $this->method($obj, 'trace');
    }

    /**
     * Write output to console browser in a nice formatted way.
     */
    public function dir($obj)
    {
        $this->method($obj, 'dir');
    }

    /**
     * Write output to console browser in a DOM element’s markup.
     */
    public function dirxml($obj)
    {
        $this->method($obj, 'dirxml');
    }

    /**
     * Write output to console browser in an easy way to run simple assertion tests.
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
        $args = implode(",", $data);

        // Echo to output
        echo "console.assert($args);";

        // Return the contents of the output buffer
        $output = ob_get_contents();

        // Store output in new array with zero index
        $_SESSION['console.log'][] = $output;

        // Stop buffering
        ob_end_clean();
    }

    /**
     * Clear the console.
     */
    public function clear()
    {
        unset($_SESSION['console.log']);
    }

    /**
     * This method is used to count the number of times it has been invoked
     * with the same provided label.
     */
    public function count($obj = 'even')
    {
        $this->method($obj, 'count');
    }

    /**
     * Start a timer with this method.
     * Optionally the time can have a label.
     */
    public function time($obj)
    {
        $this->method($obj, 'time');
    }

    /**
     * Finish a timer with this method.
     * Optionally the time can have a label.
     */
    public function timeend($obj)
    {
        $this->method($obj, 'timeEnd');
    }

    /**
     * Use this method to group console messages together with an optional label.
     */
    public function group($obj)
    {
        $this->method($obj, 'group');
    }

    /**
     * Use this method to end an group of the console messages.
     */
    public function groupend($obj = null)
    {
        $this->method($obj, 'groupEnd');
    }

    /**
     * Write output to console browser.
     */
    private function method($obj, $method = 'log')
    {
        // Start buffering
        ob_start();

        if (is_object($obj)) { // Check is an object
            $js = "var JSONObject = " . json_encode($obj) . ";\n"
                . "var JSONString = JSON.stringify(JSONObject);"
                . "var JSObject = JSON.parse(JSONString); "
                . "console.$method(JSObject);";
        } else if (is_array($obj) || $method == 'dirxml') { // Check is an array
            $js = "var data = " . json_encode($obj) . "; "
                . "console.$method(data);";
        } else if ($method == 'log') { // Check is set
            $js = "var data = '"  . $obj . "'; "
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
            $_SESSION['console.log'][] = $output;
        }

        // Stop buffering
        ob_end_clean();
    }
}
