<?php

namespace ferno\loco;

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
            throw new GrammarException("Can't make a " . __CLASS__
             . " without at least one internal parser.\n");
        }
        $this->internals = $internals;
        $this->string = 'new ' . __CLASS__ . '(' . $this->serializeArray($internals) . ')';
        parent::__construct($internals, $callback);
    }

    /**
     * default callback: return the sole result unmodified
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    public function getResult($string, $i = 0)
    {
        foreach ($this->internals as $internal) {
            try {
                $match = $internal->match($string, $i);
            } catch (ParseFailureException $e) {
                continue;
            }

            return [
                'j' => $match['j'],
                'args' => [$match['value']]
            ];
        }

        throw new ParseFailureException($this . ' could not match another token', $i, $string);
    }

    /**
     * Nullable if any internal is nullable.
     */
    public function evaluateNullability()
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
    public function firstSet()
    {
        return $this->internals;
    }
}
