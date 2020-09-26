<?php

namespace Ab\LocoX\Clause\Terminal;

use Ab\LocoX\GrammarException;
use Ab\LocoX\Exception\ParseFailureException;
use Ab\LocoX\StaticParser;

/**
 * Parser uses a regex to match itself. Regexes are time-consuming to execute,
 * so use StringParser to match static strings where possible.
 * Regexes can match multiple times in theory, but this pattern returns a singleton
 * Callback should accept an array of all the matches made
 */
class RegexParser extends StaticParser
{
    private const TARGET_REGEX_DELIM = '#';

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
     */
    public function evaluateNullability()
    {
        return 1 === preg_match($this->pattern, '', $matches);
    }

    protected static function normalizeDelimiter(string $inputRegex)
    {
        $delimiter = substr($inputRegex, 0, 1);
        if($delimiter !== self::TARGET_REGEX_DELIM) {
            $endDelimiterPosition = strrpos($inputRegex, $delimiter);

            // This will remove any escapes of the prev. delim. and replace it with our delim, then escaping it if needed
            $inputRegex = str_replace(
                ["\\$delimiter", self::TARGET_REGEX_DELIM],
                [$delimiter, "\\" . self::TARGET_REGEX_DELIM],
                $inputRegex
            );
            $inputRegex = self::TARGET_REGEX_DELIM
                . substr($inputRegex, 0, $endDelimiterPosition)
                . self::TARGET_REGEX_DELIM
                . substR($inputRegex, $endDelimiterPosition+1);
        }

    }

    public function jsonSerialize()
    {
        return ['regex/pcre' => [$this->pattern]];
    }
}
