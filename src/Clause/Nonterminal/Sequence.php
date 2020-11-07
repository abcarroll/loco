<?php declare(strict_types=1);

namespace Ab\LocoX\Clause\Nonterminal;

use Ab\LocoX\Grammar;
use Ab\LocoX\MonoParser;

/**
 * Match several things in a row. Callback should accept one argument
 * for each parser listed.
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
    public function firstSet(): array
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
     * Only nullable if everything in the list is nullable.
     *
     * This makes sense: if we have "A <- B C" with B nullable and C not nullable (or the other way around, it doesn't
     * matter) then A is only nullable if both in the sequence are: if either requires a width to the match then it will
     * by nature force A to have a width.
     */
    public function evaluateNullability(): bool
    {
        foreach ($this->internals as $internal) {
            if (false === $internal->nullable) {
                return false;
            }
        }

        return true;
    }
}
