<?php

namespace Ab\LocoX;

/**
 * Static parsers contain no internal parsers.
 */
abstract class StaticParser extends MonoParser
{
    public function __construct($callback)
    {
        parent::__construct([], $callback);
    }

    /**
     * no internals => empty immediate first-set
     */
    public function firstSet()
    {
        return [];
    }

    // empty immediate first-set => empty extended first-set
    // empty extended first-set => extended first-set cannot contain self
    // extended first-set does not contain self => not left-recursive
}
