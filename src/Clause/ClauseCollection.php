<?php declare(strict_types=1);

use Ab\LocoX\Clause;

/**
 * ClauseCollection is any [non-terminal] clause which potentially contains more than one sub-clause.
 *
 * The core collections are sequence and
 */
abstract class ClauseCollection extends Clause
{
    private array $subclause = [];

    public function children()
    {
        return $this->subclause;
    }
}
