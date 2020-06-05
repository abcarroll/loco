<?php

namespace Ab\LocoX;

/**
 * Parser uses a regex to match itself. Regexes are time-consuming to execute,
 * so use StringParser to match static strings where possible.
 * Regexes can match multiple times in theory, but this pattern returns a singleton
 * Callback should accept an array of all the matches made
 */
class RegexParser extends StaticParser
{
    private $pattern;

    public function __construct($pattern, $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '(' . var_export($pattern, true) . ')';
        if ('^' !== substr($pattern, 1, 1)) {
            throw new GrammarException($this . " doesn't anchor at the beginning of the string!");
        }
        $this->pattern = $pattern;
        parent::__construct($callback);
    }

    /**
     * default callback: return only the main match
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    /**
     * @return (mixed|string[])[]
     *
     * @psalm-return array{j: mixed, args: array<array-key, string>}
     */
    public function getResult($string, $i = 0)
    {
        if (1 === preg_match($this->pattern, substr($string, $i), $matches)) {
            return [
                'j' => $i + strlen($matches[0]),
                'args' => $matches
            ];
        }

        throw new ParseFailureException($this . ' could not match expression ' . var_export(
            $this->pattern,
            true
        ), $i, $string);
    }

    /**
     * nullable only if regex matches ""
     *
     * @return bool
     */
    public function evaluateNullability()
    {
        return 1 === preg_match($this->pattern, '', $matches);
    }
}
