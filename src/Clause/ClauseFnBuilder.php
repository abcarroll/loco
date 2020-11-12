<?php declare(strict_types=1);

/**
 * Function based builder which precisely mirrors the ClauseFactory class as simple functions.
 */

namespace Ab\LocoX\ClauseFactory {

    use Ab\LocoX\Clause\Clause;
    use Ab\LocoX\Clause\ClauseFactory;

    function seq(...$subclause): Clause
    {
        return ClauseFactory::seq(...$subclause);
    }

    function ordered_choice(...$subclause): Clause
    {
        return ClauseFactory::ordered_choice(...$subclause);
    }

    function repeat_bounded(int $min, int $max, $subclause)
    {
        return ClauseFactory::repeat_bounded($min, $max, $subclause);
    }

    function zero_or_more($subclause)
    {
        return ClauseFactory::zero_or_more($subclause);
    }

    function one_or_more($subclause)
    {
        return ClauseFactory::one_or_more($subclause);
    }

    function optional($subclause)
    {
        return ClauseFactory::optional($subclause);
    }

    function literal(string $string): Clause
    {
        return ClauseFactory::literal($string);
    }

    function regex(string $regexString): Clause
    {
        return ClauseFactory::regex($regexString);
    }

    function literal_utf8(array $untilChars = []): Clause
    {
        return ClauseFactory::literal_utf8($untilChars);
    }

    function nothing(): Clause
    {
        return ClauseFactory::nothing();
    }
}
