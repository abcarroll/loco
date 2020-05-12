<?php

namespace Ab\LocoX;

use function func_get_arg;
use function is_string;
use function strlen;
use function strpos;
use function var_export;

/**
 * Match a static string.
 * Callback should accept a single argument which is the static string in question.
 */
class StringParser extends StaticParser
{
    private string $needle;

    /**
     * StringParser constructor.
     * @param string        $needle   The text to match, as a string.
     * @param callable|null $callback
     *
     * @throws GrammarException
     */
    public function __construct(string $needle, ?callable $callback = null)
    {
        if (!is_string($needle)) {
            throw new GrammarException(
                "Can't create a " . __CLASS__ . " with 'string' " . var_export($needle, true)
            );
        }

        $this->needle = $needle;
        $this->string = 'new ' . __CLASS__ . '(' . var_export($needle, true) . ')';
        parent::__construct($callback);
    }

    /**
     * default callback: just return the string that was matched
     */
    public function defaultCallback(): string
    {
        return func_get_arg(0);
    }

    /**
     * @param string $string
     * @param int $currentPosition
     * @return array (array|int)[]
     *
     * @throws ParseFailureException
     * @psalm-return array{j: int, args: array{0: mixed}}
     */
    public function getResult(string $string, int $currentPosition = 0)
    {
        if (strpos($string, $this->needle, $currentPosition) === $currentPosition) {
            return ['j' => $currentPosition + strlen($this->needle), 'args' => [$this->needle]];
        }

        throw new ParseFailureException(
            $this . ' could not find string ' . var_export($this->needle, true),
            $currentPosition,
            $string
        );
    }

    /**
     * nullable only if string is ""
     */
    public function evaluateNullability(): bool
    {
        return '' === $this->needle;
    }
}
