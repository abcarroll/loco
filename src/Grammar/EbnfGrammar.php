<?php


namespace Ab\LocoX\Grammar;

use Exception;
use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Clause\Terminal\EmptyParser;
use Ab\LocoX\Grammar;
use Ab\LocoX\Clause\Nonterminal\BoundedRepeat;
use Ab\LocoX\Clause\Nonterminal\GreedyStarParser;
use Ab\LocoX\Clause\Nonterminal\OrderedChoice;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\StringParser;

/**
 * Takes a string presented in Extended Backus-Naur Form and turns it into a new Grammar
 * object capable of recognising the language described by that string.
 *
 * @link http://en.wikipedia.org/wiki/Extended_Backus%E2%80%93Naur_Form
 *
 * Can't handle exceptions, since these are not context-free
 * Can't handle specials, which have no clear definition
 */

// This code is in the public domain.
// http://qntm.org/locoparser

class EbnfGrammar extends Grammar
{
    public function __construct()
    {
        parent::__construct(
            '<syntax>',
            [
                '<syntax>' => new Sequence(
                    ['<space>', '<rules>'],
                    function ($space, $rules) {
                        return $rules;
                    }
                ),

                '<rules>' => new GreedyStarParser('<rule>'),

                '<rule>' => new Sequence(
                    ['<bareword>', '<space>', new StringParser('='), '<space>', '<alt>', new StringParser(';'), '<space>'],
                    function ($bareword, $space1, $equals, $space2, $alt, $semicolon, $space3) {
                        return [
                            'rule-name' => $bareword,
                            'expression' => $alt
                        ];
                    }
                ),

                '<alt>' => new Sequence(
                    ['<conc>', '<pipeconclist>'],
                    function ($conc, $pipeconclist) {
                        array_unshift($pipeconclist, $conc);

                        return new OrderedChoice($pipeconclist);
                    }
                ),

                '<pipeconclist>' => new GreedyStarParser('<pipeconc>'),

                '<pipeconc>' => new Sequence(
                    [new StringParser('|'), '<space>', '<conc>'],
                    function ($pipe, $space, $conc) {
                        return $conc;
                    }
                ),

                '<conc>' => new Sequence(
                    ['<term>', '<commatermlist>'],
                    function ($term, $commatermlist) {
                        array_unshift($commatermlist, $term);

                        // get array key numbers where multiparsers are located
                        // in reverse order so that our splicing doesn't modify the array
                        $multiparsers = [];
                        foreach ($commatermlist as $k => $internal) {
                            if ($internal instanceof BoundedRepeat) {
                                array_unshift($multiparsers, $k);
                            }
                        }

                        // We do something quite advanced here. The inner multiparsers are
                        // spliced out into the list of arguments proper instead of forming an
                        // internal sub-array of their own
                        return new Sequence(
                            $commatermlist,
                            function () use ($multiparsers) {
                                $args = func_get_args();
                                foreach ($multiparsers as $k) {
                                    array_splice($args, $k, 1, $args[$k]);
                                }

                                return $args;
                            }
                        );
                    }
                ),

                '<commatermlist>' => new GreedyStarParser('<commaterm>'),

                '<commaterm>' => new Sequence(
                    [new StringParser(','), '<space>', '<term>'],
                    function ($comma, $space, $term) {
                        return $term;
                    }
                ),

                '<term>' => new OrderedChoice(
                    ['<bareword>', '<sq>', '<dq>', '<group>', '<repetition>', '<optional>']
                ),

                '<bareword>' => new Sequence(
                    [
                        new RegexParser(
                            '#^([a-z][a-z ]*[a-z]|[a-z])#',
                            function ($match0) {
                                return $match0;
                            }
                        ),
                        '<space>'
                    ],
                    function ($bareword, $space) {
                        return $bareword;
                    }
                ),

                '<sq>' => new Sequence(
                    [
                        new RegexParser(
                            "#^'([^']*)'#",
                            function ($match0, $match1) {
                                if ('' === $match1) {
                                    return new EmptyParser();
                                }

                                return new StringParser($match1);
                            }
                        ),
                        '<space>'
                    ],
                    function ($string, $space) {
                        return $string;
                    }
                ),

                '<dq>' => new Sequence(
                    [
                        new RegexParser(
                            '#^"([^"]*)"#',
                            function ($match0, $match1) {
                                if ('' === $match1) {
                                    return new EmptyParser();
                                }

                                return new StringParser($match1);
                            }
                        ),
                        '<space>'
                    ],
                    function ($string, $space) {
                        return $string;
                    }
                ),

                '<group>' => new Sequence(
                    [
                        new StringParser('('),
                        '<space>',
                        '<alt>',
                        new StringParser(')'),
                        '<space>'
                    ],
                    function ($left_paren, $space1, $alt, $right_paren, $space2) {
                        return $alt;
                    }
                ),

                '<repetition>' => new Sequence(
                    [
                        new StringParser('{'),
                        '<space>',
                        '<alt>',
                        new StringParser('}'),
                        '<space>'
                    ],
                    function ($left_brace, $space1, $alt, $right_brace, $space2) {
                        return new GreedyStarParser($alt);
                    }
                ),

                '<optional>' => new Sequence(
                    [
                        new StringParser('['),
                        '<space>',
                        '<alt>',
                        new StringParser(']'),
                        '<space>'
                    ],
                    function ($left_bracket, $space1, $alt, $right_bracket, $space2) {
                        return new BoundedRepeat($alt, 0, 1);
                    }
                ),

                '<space>' => new GreedyStarParser('<whitespace/comment>'),

                '<whitespace/comment>' => new OrderedChoice(
                    ['<whitespace>', '<comment>']
                ),

                '<whitespace>' => new RegexParser('#^[ \t\r\n]+#'),
                '<comment>' => new RegexParser('#^(\(\* [^*]* \*\)|\(\* \*\)|\(\*\*\))#')
            ],
            function ($syntax) {
                $parsers = [];
                foreach ($syntax as $rule) {
                    if (0 === count($parsers)) {
                        $top = $rule['rule-name'];
                    }
                    $parsers[$rule['rule-name']] = $rule['expression'];
                }
                if (0 === count($parsers)) {
                    throw new Exception('No rules.');
                }

                return new Grammar($top, $parsers);
            }
        );
    }
}
