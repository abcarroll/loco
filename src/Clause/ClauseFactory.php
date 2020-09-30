<?php declare(strict_types=1);

namespace Ab\LocoX\Clause;

use Ab\LocoX\Clause\Clause;

class ClauseFactory
{
    public static function matchCharOfString(string $string)
    {
        if (strlen($string) === 0) {
            throw new \InvalidArgumentException(
                __METHOD__ . " must not be passed an empty string as behavior is ambiguous. " .
                "Use Empty clause if empty string matching is desired."
            );
        }

        return new Clause\Terminal\RegexParser('/^[' . preg_quote($string, '/') . ']+/');
    }


    public static function seq(Clause ...$sequence)
    {
        return new Clause\Nonterminal\Sequence($sequence);
    }

    /**
     * @TODO Fix into it's own clause class.
     */
    public static function ruleRef(string $ref)
    {
        return $ref;
    }

    public static function oneOrMore(Clause $subclause)
    {
        return new Clause\Nonterminal\GreedyMultiParser($subclause, 1, null);
    }

    public static function optional(Clause $subclause)
    {
        return new Clause\Nonterminal\GreedyMultiParser($subclause, 0, 1);
    }

    public static function zeroOrMore(Clause $subclause)
    {
        return new Clause\Nonterminal\GreedyMultiParser($subclause, 0, null);
    }

    /**
     * Match the first sub-clause which matches successfully, failing if zero of the sub-clauses matches.
     *
     * Ford termed this "Ordered Choice". Also called "OR" matching, "alternation", or an "any match".
     *
     * @param Clause ...$clauses
     *
     * @return Clause
     */
    public static function first(Clause ...$clauses): Clause
    {
        return new Clause\Nonterminal\LazyAltParser($clauses);
    }

    public static function followedBy(\Ab\LocoX\Clause\Clause $clause): Clause
    {
        $clause->
    }

    public static function notFollowedBy(Clause $clause): Clause
    {
    }
    
    public static function matchRegex(string $inputRegex)
    {
        return new Clause\Terminal\RegexParser($inputRegex);
    }
}
