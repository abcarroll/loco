<?php

namespace Ab\LocoX;


/**
 * http://en.wikipedia.org/wiki/Parser_combinator
 * These parsers are all unusual in that instead of returning a complete
 * set of js and tokens, each returns either a single successful combination of
 * j and result, or throws a ParseFailureException. These are, then, "monoparsers"
 */
interface ParserInterface
{
    /**
     * Parse: try to match this parser at the beginning of the string
     * Return the result only on success, or throw exception on failure
     * or if the match doesn't encompass the whole string
     *
     * @param mixed $string
     */
    public function parse($string);
}
