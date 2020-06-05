<?php

namespace Ab\LocoX;

/**
 * Abstract base-class for Parsers
 */
abstract class Parser
{
    abstract public function match(string $input, int $pos);
}
