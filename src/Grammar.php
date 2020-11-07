<?php

namespace Ab\LocoX;

use Ab\LocoX\Clause\Nonterminal\BoundedRepeat;
use Ab\LocoX\Clause\RuleReference;
use Ab\LocoX\Exception\GrammarException;

/**
 * Grammar is a container for a bunch of parsers. This container is
 * necessary so that the parser names used in the constructions of each
 * parser can actually refer to other parsers instead of just being
 * useless strings.
 */
class Grammar extends MonoParser
{
    /**
     * All parsing begins with the parser of this name.
     * $S should not be an actual parser
     */
    private $S;

    public function __construct($S, $internals, $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '(' . var_export($S, true) . ', ' . Grammar::serializeGrammar($internals) . ')';
        parent::__construct($internals, $callback);

        if (! array_key_exists($S, $this->internals)) {
            throw new GrammarException("This grammar begins with rule '" . var_export($S, true) . "' but no parser with this name was given.");
        }
        $this->S = $S;

        // Each parser may have internal sub-parsers to which it
        // "farms out" parsing duties. (This is contained in each parser's internal
        // list, $internals). In some cases, these will appear as
        // full-blown internal parsers, which is fine, but in other cases
        // (unavoidably) these will appear as mere strings, intended to refer
        // to other parsers elsewhere in the complete grammar.

        // Strings alone are no good for parsing purposes, so at this stage,
        // we resolve this by replacing each such string with a
        // reference to "the real thing" - if it can be found.

        // this needs to recurse over all inner parsers!!
        $this->resolve($this);

        // Nullability.
        // It is impossible to be certain whether an arbitrary parser is nullable
        // without knowing the nullability status of its internal parsers.
        // Because this chain may recurse, the nullability of a general collection
        // of parsers has to be evaluated by "bubbling up" nullability states
        // until we are certain that all nullable parsers have been marked as such.
        // It is not unlike a "flood fill" procedure.
        while (1) {
            foreach ($this->internals as $intlKey => $internal) {
                if (!($internal instanceof Parser)) {
                    throw new \InvalidArgumentException("$intlKey is not a parser it is a " . gettype($internal) . "($internal)");
                }
                if (true === $internal->nullable) {
                    continue;
                }

                if (! $internal->evaluateNullability()) {
                    continue;
                }

                // If we get here, then $internal is marked as non-nullable, but
                // has been newly evaluated as nullable. A change has occurred! So,
                // mark $internal as nullable now and start the process over again.
                $internal->nullable = true;

                continue 2;
            }

            // If we reach this point then we are done marking more internals as
            // nullable. The nullability fill is complete
            break;
        }

        // The reason for needing to know nullability is so that we can confidently
        // create the immediate first-set of each parser.

        // This allows the creation of the extended first-set of each parser.

        // This in turn is necessary to detect left recursion, which occurs
        // if and only if a parser contains ITSELF in its own extended first-set.
        foreach ($this->internals as $internal) {
            // Find the extended first-set of this parser. If this parser is
            // contained in its own first-set, then it is left-recursive.
            // This has to be called after the "nullability flood fill" is complete.
            $firstSet = [$internal];
            $i = 0;
            while ($i < count($firstSet)) {
                $current = $firstSet[$i];
                foreach ($current->firstSet() as $next) {

                    // Left - recursion
                    if ($next === $internal) {
                        throw new GrammarException('This grammar is left-recursive in ' . $internal . '.');
                    }

                    // If it's already in the list, then skip it
                    // this DOESN'T imply left - recursion, though
                    for ($j = 0, $jMax = count($firstSet); $j < $jMax; $j ++) {
                        if ($next === $firstSet[$j]) {
                            continue 2;
                        }
                    }

                    $firstSet[] = $next;
                }
                $i++;
            }
        }

        // Nullability is also required for this step:
        // If a BoundedRepeat's inner parser is capable of matching a
        // string of zero length, and it has an unbounded upper limit, then
        // it is going to loop forever.
        // In this situation, we raise a very serious error
        foreach ($this->internals as $internal) {
            if (! ($internal instanceof BoundedRepeat)) {
                continue;
            }
            if (null !== $internal->optional) {
                continue;
            }

            if ($internal->internals[0]->nullable) {
                throw new GrammarException($internal . ' has internal parser ' . $internal->internals[0] . ', which matches the empty string. This will cause infinite loops when parsing.');
            }
        }
    }

    /**
     * Look at all of the $internals of the supplied parser, and observe the
     * ones which are strings instead of being full-blown parsers. For each
     * string, find the actual parser here in the grammar which has that name.
     * Then, replace the string with a reference to that parser.
     * The result is that the $parser's $internals are now all (references to)
     * real parsers, no longer strings.
     * Be cautious modifying this code, it was constructed quite delicately to
     * avoid infinite loops
     *
     * @param mixed $parser
     */
    private function resolve($parser)
    {
        $keys = array_keys($parser->internals);
        for ($i = 0, $iMax = count($keys); $i < $iMax; $i ++) {
            $key = $keys[$i];

            // replace names with references
            if (is_string($parser->internals[$key]) || $parser->internals[$key] instanceof RuleReference) {

                // Make sure the other parser that we're about to create a reference to actually exists
                // > note the simplification for RuleReference: a RuleReference will return the appropriate rule name
                // > by this cast calling its __toString() method.
                $name = (string) $parser->internals[$key];
                if (!array_key_exists($name, $this->internals)) {
                    throw new Exception\GrammarException(
                        $parser . " contains a reference to another parser " . var_export($name, true) . " which cannot be found"
                    );
                }

                // create that reference
                $parser->internals[$key] = & $this->internals[$name];
            } else {
                // already a parser? No need to replace it!
                // but we do need to recurse!
                $parser->internals[$key] = $this->resolve($parser->internals[$key]);
            }
        }

        return $parser;
    }

    /**
     * default callback (this should be rarely modified) returns
     * first argument only
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    /**
     * use the "main" internal parser, S
     *
     * @param mixed $string
     * @param mixed $i
     */
    public function getResult($string, $i = 0)
    {
        $match = $this->internals[$this->S]->match($string, $i);

        return [
            'j' => $match['j'],
            'args' => [$match['value']]
        ];
    }

    /**
     * nullable iff <S> is nullable
     */
    public function evaluateNullability(): bool
    {
        return true === $this->internals[$this->S]->nullable;
    }

    /**
     * S is the first
     */
    public function firstSet(): array
    {
        return [$this->internals[$this->S]];
    }

    /**
     * a helpful internal function
     */
    public static function serializeGrammar($array)
    {
        $string = "array(";
        foreach (array_keys($array) as $keyId => $key) {
            $string .= var_export($key, true) . " => ";
            if (is_string($array[$key])) {
                $string .= var_export($array[$key], true);
            } else {
                $string .= $array[$key]->__toString();
            }
            if ($keyId + 1 !== count($array)) {
                $string .= ", ";
            }
        }
        $string .= ")";
        return $string;
    }

}
