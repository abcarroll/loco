<?php

namespace Ab\LocoX\Clause\Terminal;

use Ab\LocoX\GrammarException;
use Ab\LocoX\ParseFailureException;
use Ab\LocoX\StaticParser;

/**
 * Match a static string.
 * Callback should accept a single argument which is the static string in question.
 */
class StringParser extends StaticParser
{
    private $needle;

    public function __construct($needle, $callback = null)
    {
        if (! is_string($needle)) {
            throw new GrammarException("Can't create a " . __CLASS__ . " with 'string' " . var_export(
                $needle,
                true
            ));
        }
        $this->needle = $needle;
        $this->string = 'new ' . __CLASS__ . '(' . var_export($needle, true) . ')';
        parent::__construct($callback);
    }

    /**
     * default callback: just return the string that was matched
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    public function getResult($string, $i = 0)
    {
        if (strpos($string, $this->needle, $i) === $i) {
            return [
                'j' => $i + strlen($this->needle),
                'args' => [$this->needle]
            ];
        }

        throw new ParseFailureException($this . ' could not find string ' . var_export(
            $this->needle,
            true
        ), $i, $string);
    }

    /**
     * nullable only if string is ""
     */
    public function evaluateNullability()
    {
        return '' === $this->needle;
    }
}
