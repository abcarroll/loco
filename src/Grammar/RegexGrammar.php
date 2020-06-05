<?php


namespace Ab\LocoX\Grammar;

use Exception;
use Ab\LocoX\ConcParser;
use Ab\LocoX\EmptyParser;
use Ab\LocoX\Grammar;
use Ab\LocoX\Grammar\Regex\CharClass;
use Ab\LocoX\Grammar\Regex\Conc;
use Ab\LocoX\Grammar\Regex\Mult;
use Ab\LocoX\Grammar\Regex\Multiplier;
use Ab\LocoX\Grammar\Regex\Pattern;
use Ab\LocoX\GreedyStarParser;
use Ab\LocoX\LazyAltParser;
use Ab\LocoX\RegexParser;
use Ab\LocoX\StringParser;

class RegexGrammar extends Grammar
{
    public function __construct()
    {
        parent::__construct(
            '<pattern>',
            [
                // A Pattern is an alternation between several Concs, separated by pipes.
                '<pattern>' => new ConcParser(
                    ['<conc>', '<pipeconclist>'],
                    function ($conc, $pipeconclist) {
                        array_unshift($pipeconclist, $conc);

                        return new Pattern($pipeconclist);
                    }
                ),
                '<pipeconclist>' => new GreedyStarParser(
                    '<pipeconc>'
                ),
                '<pipeconc>' => new ConcParser(
                    [
                        new StringParser('|'),
                        '<conc>'
                    ],
                    function ($pipe, $conc) { return $conc; }
                ),

                // A Conc is a concatenation of several Mults.
                '<conc>' => new GreedyStarParser(
                    '<mult>',
                    function () { return new Conc(func_get_args()); }
                ),

                // A Mult is a multiplicand (CharClass or sub-Pattern) followed by a multiplier.
                // A subpattern has to be put inside parentheses.
                '<mult>' => new ConcParser(
                    ['<multiplicand>', '<multiplier>'],
                    function ($multiplicand, $multiplier) { return new Mult($multiplicand, $multiplier); }
                ),
                '<multiplicand>' => new LazyAltParser(
                    ['<subpattern>', '<charclass>']
                ),
                '<subpattern>' => new ConcParser(
                    [new StringParser('('), '<pattern>', new StringParser(')')],
                    function ($left_parenthesis, $pattern, $right_parenthesis) { return $pattern; }
                ),

                // A Multiplier has a lower bound and an upper bound. There are several short forms.
                // In the absence of a multiplier, {1,1} is assumed
                '<multiplier>' => new LazyAltParser(
                    [
                        '<bracemultiplier>',
                        new StringParser('?', function ($string) { return new Multiplier(0, 1); }),
                        new StringParser('*', function ($string) { return new Multiplier(0, null); }),
                        new StringParser('+', function ($string) { return new Multiplier(1, null); }),
                        new  EmptyParser(function () { return new Multiplier(1, 1); })
                    ]
                ),

                '<bracemultiplier>' => new ConcParser(
                    [
                        new StringParser('{'),
                        '<multiplierinterior>',
                        new StringParser('}')
                    ],
                    function ($left_brace, $multiplierinterior, $right_brace) { return $multiplierinterior; }
                ),

                '<multiplierinterior>' => new LazyAltParser(
                    ['<bothbounds>', '<unlimited>', '<onebound>']
                ),
                '<bothbounds>' => new ConcParser(
                    ['<integer>', 'COMMA', '<integer>'],
                    function ($integer1, $comma, $integer2) { return new Multiplier($integer1, $integer2); }
                ),
                '<unlimited>' => new ConcParser(
                    ['<integer>', 'COMMA'],
                    function ($integer, $comma) { return new Multiplier($integer, null); }
                ),
                '<onebound>' => new ConcParser(
                    ['<integer>'],
                    function ($integer) { return new Multiplier($integer, $integer); }
                ),
                'COMMA' => new StringParser(','),
                '<integer>' => new RegexParser('#^(0|[1-9][0-9]*)#', function ($match) { return (int) ($match); }),

                // A CharClass is usually a single literal character.
                // It can also be a single character escaped with a backslash,
                // or a "true" charclass, which is a possibly-negated set of elements
                // listed inside a pair of brackets.
                '<charclass>' => new LazyAltParser(
                    [
                        new RegexParser('#^[^|()\\[\\]?*+{}\\\\.]#', function ($match) { return new CharClass($match); }),
                        '<bracketednegatedcharclass>',
                        '<bracketedcharclass>',
                        new StringParser('\\|', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\(', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\)', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\[', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\]', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\?', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\*', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\+', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\{', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\}', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\\\', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\.', function ($string) { return new CharClass(substr($string, 1, 1)); }),
                        new StringParser('\\f', function ($string) { return new CharClass("\f"); }),
                        new StringParser('\\n', function ($string) { return new CharClass("\n"); }),
                        new StringParser('\\r', function ($string) { return new CharClass("\r"); }),
                        new StringParser('\\t', function ($string) { return new CharClass("\t"); }),
                        new StringParser('\\v', function ($string) { return new CharClass("\v"); }),
                        new StringParser('\\w', function ($string) { return new CharClass('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'); }),
                        new StringParser('\\W', function ($string) { return new CharClass('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz', true); }),
                        new StringParser('\\d', function ($string) { return new CharClass('0123456789'); }),
                        new StringParser('\\D', function ($string) { return new CharClass('0123456789', true); }),
                        new StringParser('\\s', function ($string) { return new CharClass(" \f\n\r\t\v"); }),
                        new StringParser('\\S', function ($string) { return new CharClass(" \f\n\r\t\v", true); }),
                        new StringParser('.', function ($string) { return new CharClass('', true); })
                    ]
                ),

                '<bracketednegatedcharclass>' => new ConcParser(
                    ['LEFT_BRACKET', 'CARET', '<elemlist>', 'RIGHT_BRACKET'],
                    function ($left_bracket, $elemlist, $right_bracket) { return new CharClass($elemlist, true); }
                ),
                '<bracketedcharclass>' => new ConcParser(
                    ['LEFT_BRACKET', '<elemlist>', 'RIGHT_BRACKET'],
                    function ($left_bracket, $elemlist, $right_bracket) { return new CharClass($elemlist); }
                ),
                'LEFT_BRACKET' => new StringParser('['),
                'RIGHT_BRACKET' => new StringParser(']'),
                'CARET' => new StringParser('^'),

                // A true charclass may be negated with a leading caret.
                '<elemlist>' => new GreedyStarParser(
                    '<elem>',
                    function () { return implode('', func_get_args()); }
                ),

                // An element is either a single character or a character range.
                // A character range is represented with an optional hyphen
                '<elem>' => new LazyAltParser(
                    ['<charrange>', '<classchar>']
                ),

                '<charrange>' => new ConcParser(
                    ['<classchar>', 'HYPHEN', '<classchar>'],
                    function ($char1, $hyphen, $char2) {
                        $char1 = ord($char1);
                        $char2 = ord($char2);
                        if ($char2 < $char1) {
                            throw new Exception('Disordered range');
                        }
                        $string = '';
                        for ($ord = $char1; $ord <= $char2; $ord++) {
                            $string .= chr($ord);
                        }

                        return $string;
                    }
                ),
                'HYPHEN' => new StringParser('-'),

                // interior characters in character classes usually represent themselves,
                // but some are backslash-escaped
                '<classchar>' => new LazyAltParser(
                    [
                        new RegexParser('#^[^\\\\\\[\\]\\^\\-]#'),
                        new StringParser('\\\\', function ($string) { return substr($string, 1, 1); }),
                        new StringParser('\\[', function ($string) { return substr($string, 1, 1); }),
                        new StringParser('\\]', function ($string) { return substr($string, 1, 1); }),
                        new StringParser('\\^', function ($string) { return substr($string, 1, 1); }),
                        new StringParser('\\-', function ($string) { return substr($string, 1, 1); }),
                        new StringParser('\\f', function ($string) { return "\f"; }),
                        new StringParser('\\n', function ($string) { return "\n"; }),
                        new StringParser('\\r', function ($string) { return "\r"; }),
                        new StringParser('\\t', function ($string) { return "\t"; }),
                        new StringParser('\\v', function ($string) { return "\v"; })
                    ]
                )
            ]
        );
    }
}
