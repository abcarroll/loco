<?php

namespace Ab\LocoX;

/**
 * LookaheadParser matches everything up until any one of the provided
 * $lookaheadStrings is encountered. So this works like a negative
 * lookahead regular expression, but is less flexible.
 */
class LookaheadParser extends StaticParser
{
    private $lookaheadStrings;

    public function __construct($lookaheadStrings, $callback = null)
    {
        if (! is_array($lookaheadStrings)) {
            throw new GrammarException('$lookaheadStrings must be an array');
        } elseif (0 === count($lookaheadStrings)) {
            throw new GrammarException('$lookaheadStrings must not be empty');
        }
        $this->lookaheadStrings = $lookaheadStrings;

        $this->string = 'new ' . __CLASS__ . '(' . serialiseArray($lookaheadStrings) . ')';

        parent::__construct($callback);
    }

    /**
     * default callback: return the string that was matched
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    public function getResult($string, $i = 0)
    {
        $lookaheadFirstStringPos = strlen($string);
        foreach ($this->lookaheadStrings as $lookahead) {
            $pos = strpos($string, $lookahead, $i);
            if (false !== $pos && $pos < $lookaheadFirstStringPos) {
                $lookaheadFirstStringPos = $pos;
            }
        }

        if ($lookaheadFirstStringPos === $i) {
            throw new ParseFailureException($this . ' did not match anything ', $i, $string);
        }

        return [
            'j' => $lookaheadFirstStringPos,
            'args' => [substr($string, $i, $lookaheadFirstStringPos - $i)],
        ];
    }

    public function evaluateNullability()
    {
        return false;
    }
}
