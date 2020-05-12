<?php

namespace Ab\LocoX;

use function count;
use function func_get_arg;

/**
 * Takes the input parsers and applies them all in turn. "Lazy" indicates
 * that as soon as a single parser matches, those matches are returned and
 * processing halts.
 * This is best used when the input parsers are mutually exclusive
 * callback should accept a single argument which is the single match
 * LazyAltParsers become risky when one is a proper prefix of another
 */
class LazyAltParser extends MonoParser
{
    public function __construct($internals, $callback = null)
    {
        if (0 === count($internals)) {
            throw new GrammarException("Can't make a " . __CLASS__ . " without at least one internal parser.\n");
        }
        $this->internals = $internals;
        $this->string = 'new ' . __CLASS__ . '(' . serialiseArray($internals) . ')';
        parent::__construct($internals, $callback);
    }

    /**
     * default callback: return the sole result unmodified
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    /**
     * @return array (array|mixed)[]
     *
     * @psalm-return array{j: mixed, args: array{0: mixed}}
     */
    public function getResult(string $string, int $currentPosition = 0): array
    {
        foreach ($this->internals as $internal) {
            try {
                $match = $internal->match($string, $currentPosition);
            } catch (ParseFailureException $e) {
                continue;
            }

            return ['j' => $match['j'], 'args' => [$match['value']]];
        }

        throw new ParseFailureException($this . ' could not match another token', $currentPosition, $string);
    }

    /**
     * Nullable if any internal is nullable.
     */
    public function evaluateNullability(): bool
    {
        foreach ($this->internals as $internal) {
            if ($internal->nullable) {
                return true;
            }
        }

        return false;
    }

    /**
     * every internal is potentially a first.
     */
    public function firstSet(): array
    {
        return $this->internals;
    }
}
