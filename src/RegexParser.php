<?php

namespace Ab\LocoX;

use function func_get_arg;
use function preg_match;
use function strlen;
use function substr;
use function var_export;

/**
 * Parser uses a regex to match itself. Regexes are time-consuming to execute,
 * so use StringParser to match static strings where possible.
 * Regexes can match multiple times in theory, but this pattern returns a singleton
 * Callback should accept an array of all the matches made
 */
class RegexParser extends StaticParser
{
    private string $pattern;

    /**
     * RegexParser constructor.
     * @param string $pattern
     * @param callable|null $callback
     * @throws GrammarException
     */
    public function __construct(string $pattern, ?callable $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '(' . var_export($pattern, true) . ')';

        // TODO Just anchor it automatically.
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
     * @return (int|string[])[]
     *
     * @psalm-return array{j: int, args: array<array-key, string>}
     */
    public function getResult(string $string, int $currentPosition = 0)
    {
        if (1 === preg_match($this->pattern, substr($string, $currentPosition), $matches)) {
            return ['j' => $currentPosition + strlen($matches[0]), 'args' => $matches];
        }

        throw new ParseFailureException($this . ' could not match expression ' . var_export(
                $this->pattern,
                true
            ), $currentPosition, $string);
    }

    /**
     * nullable only if regex matches ""
     */
    public function evaluateNullability(): bool
    {
        return 1 === preg_match($this->pattern, '', $matches);
    }

}
