<?php

namespace Ab\LocoX\Clause\Nonterminal;

use Ab\LocoX\Grammar;
use Ab\LocoX\MonoParser;

/**
 * A "sequence" clause, also called "all" match or "AND" match: All sub-clause must match in order
 *
 * Matches the input at a given start position if all of its subclauses match the input in order, with the first
 * subclause match starting at the initial position, and each subsequent subclause match starting immediately after the
 * previous subclause match.  Matching stops if a single subclause fails to match the input at its start position.
 *
 * The semantic action callback shall be passed one argument for each subclause given.
 */
class Sequence extends MonoParser
{
    public function __construct($internals, $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '(' . Grammar::serializeGrammar($internals) . ')';
        parent::__construct($internals, $callback);
    }

    /**
     * Default callback (this should be used rarely) returns all arguments as
     * an array. In the majority of cases the user should specify a callback.
     */
    public function defaultCallback()
    {
        return func_get_args();
    }

    public function getResult($string, $i = 0)
    {
        $j = $i;
        $args = [];
        foreach ($this->internals as $parser) {
            $match = $parser->match($string, $j);
            $j = $match['j'];
            $args[] = $match['value'];
        }

        return ['j' => $j, 'args' => $args];
    }

    /**
     * First-set is built up as follows...
     */
    public function firstSet()
    {
        $firstSet = array();
        foreach ($this->internals as $internal) {
            // The first $internal is always in the first-set
            $firstSet[] = $internal;
            // If $internal was nullable, then the next internal in the
            // list is also in the first-set, so continue the loop.
            // Otherwise we are done.
            if (!$internal->nullable) {
                break;
            }
        }

        return $firstSet;
    }
    /**
     * only nullable if everything in the list is nullable
     */
    public function evaluateNullability()
    {
        foreach ($this->internals as $internal) {
            if (!$internal->nullable) {
                return false;
            }
        }
        return true;
    }

    public function validateCallbackType()
    {
        //new \ReflectionFunction($this->)
    }

    public function jsonSerialize()
    {
        return ['seq' => $this->internals];
    }
}
