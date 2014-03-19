<?php

namespace Crawler;

/**
 * Class Evaluate
 *
 * utility class that provides evaluation methods
 */
class Evaluate
{
    /**
     * @param $val1
     * @param $val2
     *
     * @return mixed
     */
    private static function add($val1, $val2)
    {
        return $val1 + $val2;
    }

    /**
     * @param $value
     *
     * @return number
     */
    private static function abs($value)
    {
        return abs($value);
    }

    /**
     * @param $val1
     * @param $val2
     *
     * @return mixed
     */
    private static function multiply($val1, $val2)
    {
        return $val1 * $val2;
    }

    /**
     * @param $val1
     * @param $val2
     *
     * @return mixed
     */
    private static function subtract($val1, $val2)
    {
        return $val1 - $val2;
    }

    /**
     * finds an executes an expression that has integers for arguments
     *
     * @param $expression
     *
     * @return mixed
     */
    private static function executeNextMethod($expression)
    {
        // Breaks
        return preg_replace_callback(
            '/([a-z]+)\((-?[0-9]+)(,-?[0-9]+)?\)/',
            function ($match) {
                if (isset($match[3])) {
                    $match[3] = ltrim($match[3], ',');
                }

                switch ($match[1]) {
                    case 'add':
                        return self::add($match[2], $match[3]);
                        break;
                    case 'multiply':
                        return self::multiply($match[2], $match[3]);
                        break;
                    case 'subtract':
                        return self::subtract($match[2], $match[3]);
                        break;
                    case 'abs':
                        return self::abs($match[2]);
                        break;
                }
            },
            $expression,
            1
        );
    }

    /**
     * takes an expression and processes each method, returning a single integer
     *
     * @param $expression
     *
     * @return int
     */
    public static function process($expression)
    {
        // For each open parentheses ( in the string, locate and execute the next method that can be executed
        for ($x = 0, $count = preg_match_all('/\(/', $expression); $x < $count; $x++) {
            $expression = self::executeNextMethod($expression);
        }

        // Cast the final string as an integer so json_encode will not surround with quotes
        return (int) $expression;
    }
}
