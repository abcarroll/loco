<?php


namespace Ferno\Loco\Grammar;

use Exception;
use Ferno\Loco\ConcParser;
use Ferno\Loco\EmptyParser;
use Ferno\Loco\Grammar;
use Ferno\Loco\GreedyMultiParser;
use Ferno\Loco\GreedyStarParser;
use Ferno\Loco\LazyAltParser;
use Ferno\Loco\RegexParser;
use Ferno\Loco\StringParser;

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
                '<syntax>' => new ConcParser(
                    [
                        '<rules>',
                        'OPT-WHITESPACE'
                    ],
                    function ($rules, $whitespace) { return $rules; }
                ),

                '<rules>' => new GreedyMultiParser(
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

                '<ruleoremptyline>' => new LazyAltParser(
                    ['<rule>', '<emptyline>']
                ),

                '<emptyline>' => new ConcParser(
                    ['OPT-WHITESPACE', 'EOL'],
                    function ($whitespace, $eol) {
                    }
                ),

                '<rule>' => new ConcParser(
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

                '<expression>' => new ConcParser(
                    [
                        '<list>',
                        '<pipelists>'
                    ],
                    function ($list, $pipelists) {
                        array_unshift($pipelists, $list);

                        return new LazyAltParser($pipelists);
                    }
                ),

                '<pipelists>' => new GreedyStarParser('<pipelist>'),

                '<pipelist>' => new ConcParser(
                    [
                        new StringParser('|'),
                        'OPT-WHITESPACE',
                        '<list>'
                    ],
                    function ($pipe, $whitespace, $list) {
                        return $list;
                    }
                ),

                '<list>' => new GreedyMultiParser(
                    '<term>',
                    1,
                    null,
                    function () {
                        return new ConcParser(func_get_args());
                    }
                ),

                '<term>' => new ConcParser(
                    ['TERM', 'OPT-WHITESPACE'],
                    function ($term, $whitespace) {
                        return $term;
                    }
                ),

                'TERM' => new LazyAltParser(
                    [
                        'LITERAL',
                        'RULE-NAME'
                    ]
                ),

                'LITERAL' => new LazyAltParser(
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

                'EOL' => new LazyAltParser(
                    [
                        new StringParser("\r"),
                        new StringParser("\n")
                    ]
                )
            ],
            function ($syntax) {
                $parsers = [];
                $top = null;
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
