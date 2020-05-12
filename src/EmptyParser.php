<?php

namespace Ab\LocoX;

/**
 * Match the empty string
 */
class EmptyParser extends StaticParser
{
    public function __construct(?callable $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '()';
        parent::__construct($callback);
    }

    /**
     * default callback returns null
     */
    public function defaultCallback(): void
    {
    }

    /**
     * Always match successfully, pass no args to callback
     *
     * @param mixed $string
     * @param mixed $currentPosition
     *
     * @return array (array|mixed)[]
     *
     * @psalm-return array{j: mixed, args: array<empty, empty>}
     */
    public function getResult(string $string, int $currentPosition = 0): array
    {
        return ['j' => $currentPosition, 'args' => []];
    }

    /**
     * emptyparser is nullable.
     *
     * @return true
     */
    public function evaluateNullability(): bool
    {
        return true;
    }
}
