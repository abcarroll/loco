<?php

namespace Ab\LocoX;

use Ab\LocoX\Exception\GrammarException;
use function func_get_args;

/**
 * Match several things in a row. Callback should accept one argument
 * for each parser listed.
 */
class ConcParser extends MonoParser
{
    /**
     * @param ParserInterface[] $internals
     * @param null|callable $callback
     *
     * @throws GrammarException
     */
    public function __construct(array $internals, $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '(' . serialiseArray($internals) . ')';
        parent::__construct($internals, $callback);
    }

    /**
     * Default callback (this should be used rarely) returns all arguments as
     * an array. In the majority of cases the user should specify a callback.
     *
     * @psalm-return list<mixed>
     */
    public function defaultCallback(): array
    {
        return func_get_args();
    }

    /**
     * @param string $string
     * @param int $currentPosition
     * @return array (array|mixed)[]
     *
     * @psalm-return array{j: mixed, args: list<mixed>}
     */
    public function getResult(string $string, int $currentPosition = 0): array
    {
        $j = $currentPosition;
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
     *
     * @psalm-return list<MonoParser>
     */
    public function firstSet(): array
    {
        $firstSet = [];
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
    public function evaluateNullability(): bool
    {
        foreach ($this->internals as $internal) {
            if (!$internal->nullable) {
                return false;
            }
        }

        return true;
    }
}
