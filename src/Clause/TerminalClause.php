<?php declare(strict_types=1);

namespace Ab\LocoX\Clause;

use Ab\LocoX\MonoParser;

/**
 * Called "StaticParser" in the original Loco, these are Terminals.
 *
 * Another way to say it is a TerminalClause contain no internal parsers.
 */
abstract class TerminalClause extends MonoParser
{
    public function __construct($callback)
    {
        parent::__construct([], $callback);
    }

    /**
     * no internals => empty immediate first-set
     */
    public function firstSet(): array
    {
        return [];
    }

    // empty immediate first-set => empty extended first-set
    // empty extended first-set => extended first-set cannot contain self
    // extended first-set does not contain self => not left-recursive
}
