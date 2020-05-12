<?php

namespace Ab\LocoX;

/**
 * Match the empty string
 */
class EmptyParser extends \Ab\LocoX\StaticParser
{
    public function __construct($callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '()';
        parent::__construct($callback);
    }

    /**
     * default callback returns null
     *
     * @return void
     */
    public function defaultCallback()
    {
    }

    /**
     * Always match successfully, pass no args to callback
     *
     * @param mixed $string
     * @param mixed $i
     *
     * @return (array|mixed)[]
     *
     * @psalm-return array{j: mixed, args: array<empty, empty>}
     */
    public function getResult($string, $i = 0)
    {
        return ['j' => $i, 'args' => []];
    }

    /**
     * emptyparser is nullable.
     *
     * @return true
     */
    public function evaluateNullability()
    {
        return true;
    }
}
