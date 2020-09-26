<?php

namespace Ab\LocoX\Clause;

use Ab\LocoX\Parser;

abstract class Clause extends Parser
{
    /**
     * Every parser assumes that it is non-nullable from the outset
     */
    public bool $nullable = false;

    /**
     * The default semantic action.
     *
     * Note this may be any callable, including a callable/invokable class. If you are providing non-standard actions,
     * please consult the documentation on how to provide appropriate verification.
     *
     * @var callable
     */
    private $action;

    /**
     * The immediate first-set of a parser is the set of all internal parsers
     * which could be matched first. For example, if A = B . C then the first-set
     * of A is usually {B}. If B is nullable, then C could also be matched first, so the
     * first-set is {B, C}.
     * This has to be called after the "nullability flood fill" is complete,
     * or "Called method of non-object" exceptions will arise
     */
    abstract public function firstSet();

    /**
     * Evaluate the nullability of this parser with respect to each of its
     * internals. This function must NOT simply "return $nullable;", whose content
     * may be out of date; this function must NOT modify $nullable, either, because
     * that is not for this function to do; this function must NOT recursively
     * call evaluateNullability() on any of its internals because that could easily
     * result in a stack overflow.
     * Just gets $nullable for each internal, if any.
     * This has to be called after all strings have been resolved to parser references.
     */
    abstract public function evaluateNullability();

    /**
     * Test to see if the clause will match on the given string
     *
     * @param     $string
     * @param int $i
     *
     * @return mixed
     */
    abstract public function match($string, $i = 0);

    public function getAction(): callable
    {
        return $this->action;
    }

    abstract public function jsonSerialize();

    public function children()
    {
        return [];
    }
}
