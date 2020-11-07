<?php declare(strict_types=1);

namespace Ab\LocoX\Clause\Nonterminal;

/**
 * Tiny subclass is ironically much more useful than BoundedRepeat
 */
class GreedyStarParser extends BoundedRepeat
{
    public function __construct($internal, $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '(' . $internal . ')';
        parent::__construct($internal, 0, null, $callback);
    }
}
