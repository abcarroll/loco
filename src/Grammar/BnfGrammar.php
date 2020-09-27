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
 * Takes a string presented in Backus-Naur Form and turns it into a new Grammar
 * object capable of recognising the language described by that string.
 *
 * @link http://en.wikipedia.org/wiki/Backus%E2%80%93Naur_Form
 */

// This code is in the public domain.
// http://qntm.org/locoparser
class BnfGrammar extends Grammar
{
    public function __construct()
    {
        parent::__construct(
            '<syntax>',
            [
                '<syntax>' => new Sequence(
                    [
                        '<rules>',
                        'OPT-WHITESPACE'
                    ],
                    function ($rules, $whitespace) { return $rules; }
                ),

                '<rules>' => new BoundedRepeat(
                    '<ruleoremptyline>',
                    1,
                    null,
                    function () {
                        $rules = [];
                        foreach (func_get_args() as $rule) {

                            // blank line
                            if (null === $rule) {
                                continue;
                            }

                            $rules[] = $rule;
                        }

                        return $rules;
                    }
                ),

                '<ruleoremptyline>' => new OrderedChoice(
                    ['<rule>', '<emptyline>']
                ),

                '<emptyline>' => new Sequence(
                    ['OPT-WHITESPACE', 'EOL'],
                    function ($whitespace, $eol) {
                    }
                ),

                '<rule>' => new Sequence(
                    [
                        'OPT-WHITESPACE',
                        'RULE-NAME',
                        'OPT-WHITESPACE',
                        new StringParser('::='),
                        'OPT-WHITESPACE',
                        '<expression>',
                        'EOL'
                    ],
                    function (
                        $whitespace1,
                        $rule_name,
                        $whitespace2,
                        $equals,
                        $whitespace3,
                        $expression,
                        $eol
                    ) {
                        return [
                            'rule-name' => $rule_name,
                            'expression' => $expression
                        ];
                    }
                ),

                '<expression>' => new Sequence(
                    [
                        '<list>',
                        '<pipelists>'
                    ],
                    function ($list, $pipelists) {
                        array_unshift($pipelists, $list);

                        return new OrderedChoice($pipelists);
                    }
                ),

                '<pipelists>' => new GreedyStarParser('<pipelist>'),

                '<pipelist>' => new Sequence(
                    [
                        new StringParser('|'),
                        'OPT-WHITESPACE',
                        '<list>'
                    ],
                    function ($pipe, $whitespace, $list) {
                        return $list;
                    }
                ),

                '<list>' => new BoundedRepeat(
                    '<term>',
                    1,
                    null,
                    function () {
                        return new Sequence(func_get_args());
                    }
                ),

                '<term>' => new Sequence(
                    ['TERM', 'OPT-WHITESPACE'],
                    function ($term, $whitespace) {
                        return $term;
                    }
                ),

                'TERM' => new OrderedChoice(
                    [
                        'LITERAL',
                        'RULE-NAME'
                    ]
                ),

                'LITERAL' => new OrderedChoice(
                    [
                        new RegexParser('#^"([^"]*)"#', function ($match0, $match1) { return $match1; }),
                        new RegexParser("#^'([^']*)'#", function ($match0, $match1) { return $match1; })
                    ],
                    function ($text) {
                        if ('' === $text) {
                            return new EmptyParser(function () { return ''; });
                        }

                        return new StringParser($text);
                    }
                ),

                'RULE-NAME' => new RegexParser('#^<[A-Za-z\\-]*>#'),

                'OPT-WHITESPACE' => new RegexParser("#^[\t ]*#"),

                'EOL' => new OrderedChoice(
                    [
                        new StringParser("\r"),
                        new StringParser("\n")
                    ]
                )
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
