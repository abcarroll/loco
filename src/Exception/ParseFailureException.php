<?php

namespace Ab\LocoX\Exception;

use Exception;
use RuntimeException;

/**
 * Occurs when any parser fails to parse what it's supposed to
 * parse. Usually non-fatal and almost always caught
 */
class ParseFailureException extends RuntimeException
{
    public $i;

    public function __construct(string $message, $i, string $parseInput, $code = 0, Exception $previous = null)
    {
        $this->i = $i;
        $message .= ' at position ' . var_export($i, true) . ' in string ' . var_export($parseInput, true);
        parent::__construct($message, $code);
    }

    const _CLASS = __CLASS__;
}
