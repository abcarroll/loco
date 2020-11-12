<?php declare(strict_types=1);

namespace Ab\LocoX\Clause;

use Ab\LocoX\Clause\Nonterminal\BoundedRepeat;
use Ab\LocoX\Clause\Nonterminal\OrderedChoice;
use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Clause\Terminal\EmptyParser;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\StringParser;
use Ab\LocoX\Clause\Terminal\Utf8Parser;

/**
 * Factory class for clause objects
 */
final class ClauseFactory
{
    private function __construct()
    {
        /* Prevent instantiation */
    }

    /**
     * @param mixed ...$subclause
     *
     * @return Clause
     */
    public static function seq(Clause ...$subclause): Clause
    {
        if (count($subclause) > 1) {
            return new Sequence($subclause);
        }

        return $subclause[0] ?? self::nothing();
    }

    /**
     * Match the first sub-clause which matches successfully, failing if zero of the sub-clauses matches.
     *
     * Ford termed this "Ordered Choice". Also called "OR" matching, "alternation", or an "any match", or "First"
     *
     * @param Clause ...$clauses
     *
     * @return Clause
     */
    public static function ordered_choice(...$subclause): Clause
    {
        return new OrderedChoice($subclause);
    }

    /**
     * @param int $min
     * @param int $max
     * @param     $subclause
     *
     * @return BoundedRepeat
     */
    public static function repeat_bounded(int $min, int $max, $subclause): Clause
    {
        // TODO Move this logic to the actual BoundedRepeat class
        if ($max === +INF) {
            $max = null;
        }

        return new BoundedRepeat($subclause, $min, $max);
    }

    /**
     * @param $subclause
     *
     * @return BoundedRepeat
     */
    public static function zero_or_more(Clause $subclause): Clause
    {
        return self::repeat_bounded(0, INF, $subclause);
    }

    /**
     * @param $subclause
     *
     * @return BoundedRepeat
     */
    public static function one_or_more(Clause $subclause): Clause
    {
        return self::repeat_bounded(1, INF, $subclause);
    }

    /**
     * @param $subclause
     *
     * @return BoundedRepeat
     */
    public static function optional(Clause $subclause): Clause
    {
        return self::repeat_bounded(0, 1, $subclause);
    }

    /**
     * @param string $string
     *
     * @return Clause
     */
    public static function literal(string $string): Clause
    {
        return new StringParser($string);
    }

    /**
     * @param string $regexString
     *
     * @return Clause
     */
    public static function regex(string $regexString): Clause
    {
        // Regular Expression clauses must be anchored or it will fail.
        // This likely should just be a part of the actual clause match, but
        // that changes behaviour.
        if (substr($regexString, 1, 1) !== '^') {
            $regexString = substr($regexString, 0, 1) . '^' . substr($regexString, 1);
        }
        return new RegexParser($regexString);
    }

    /**
     * @param array $untilChars
     *
     * @return Clause
     */
    public static function literal_utf8(array $untilChars = []): Clause
    {
        return new Utf8Parser($untilChars);
    }

    /**
     * @return Clause
     */
    public static function nothing(): Clause
    {
        return new EmptyParser();
    }

    public static function ref(string $refName)
    {
        return new Sequence([$refName]);
    }
}
