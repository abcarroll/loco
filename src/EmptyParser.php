<?php

namespace Ferno\Loco;

/**
 * Match the empty string
 */
class EmptyParser extends StaticParser
{
    public function __construct($callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '()';
        parent::__construct($callback);
    }

    /**
     * default callback returns null
     */
    public function defaultCallback()
    {
    }

    /**
     * Always match successfully, pass no args to callback
     *
     * @param mixed $string
     * @param mixed $i
     */
    public function getResult($string, $i = 0)
    {
        return [
            'j' => $i,
            'args' => []
        ];
    }

    /**
     * emptyparser is nullable.
     */
    public function evaluateNullability()
    {
        return true;
    }
}
