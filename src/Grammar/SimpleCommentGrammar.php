<?php


namespace Ab\LocoX\Grammar;

use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Grammar;
use Ab\LocoX\Clause\Nonterminal\GreedyStarParser;
use Ab\LocoX\Clause\Nonterminal\OrderedChoice;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\StringParser;
use Ab\LocoX\Clause\Terminal\Utf8Parser;

class SimpleCommentGrammar extends Grammar
{
    public function __construct()
    {
        parent::__construct(
            '<comment>',
            [
                '<comment>' => new GreedyStarParser(
                    '<blockorwhitespace>',
                    function () { return implode('', func_get_args()); }
                ),
                '<blockorwhitespace>' => new OrderedChoice(
                    ['<h5>', '<p>', 'WHITESPACE']
                ),
                '<p>' => new Sequence(
                    ['OPEN_P', '<text>', 'CLOSE_P'],
                    function ($open_p, $text, $close_p) { return $open_p . $text . $close_p; }
                ),
                '<h5>' => new Sequence(
                    ['OPEN_H5', '<text>', 'CLOSE_H5'],
                    function ($open_h5, $text, $close_h5) { return $open_h5 . $text . $close_h5; }
                ),
                '<strong>' => new Sequence(
                    ['OPEN_STRONG', '<text>', 'CLOSE_STRONG'],
                    function ($open_strong, $text, $close_strong) { return $open_strong . $text . $close_strong; }
                ),
                '<em>' => new Sequence(
                    ['OPEN_EM', '<text>', 'CLOSE_EM'],
                    function ($open_em, $text, $close_em) { return $open_em . $text . $close_em; }
                ),
                '<text>' => new GreedyStarParser(
                    '<atom>',
                    function () { return implode('', func_get_args()); }
                ),
                '<atom>' => new OrderedChoice(
                    ['<char>', '<strong>', '<em>', 'FULL_BR']
                ),
                '<char>' => new OrderedChoice(
                    ['UTF8_EXCEPT', 'GREATER_THAN', 'LESS_THAN', 'AMPERSAND']
                ),

                // actual lexables here

                'WHITESPACE' => new RegexParser("#^[ \n\r\t]+#"),
                'OPEN_P' => new RegexParser("#^<p[ \n\r\t]*>#"),
                'CLOSE_P' => new RegexParser("#^</p[ \n\r\t]*>#"),
                'OPEN_H5' => new RegexParser("#^<h5[ \n\r\t]*>#"),
                'CLOSE_H5' => new RegexParser("#^</h5[ \n\r\t]*>#"),
                'OPEN_EM' => new RegexParser("#^<em[ \n\r\t]*>#"),
                'CLOSE_EM' => new RegexParser("#^</em[ \n\r\t]*>#"),
                'OPEN_STRONG' => new RegexParser("#^<strong[ \n\r\t]*>#"),
                'CLOSE_STRONG' => new RegexParser("#^</strong[ \n\r\t]*>#"),
                'FULL_BR' => new RegexParser("#^<br[ \n\r\t]*/>#"),

                'UTF8_EXCEPT' => new   Utf8Parser(['<', '>', '&']), // any UTF-8 character except <, > or &
                'GREATER_THAN' => new StringParser('&gt;'),               // ... or an escaped >
                'LESS_THAN' => new StringParser('&lt;'),               // ... or an escaped <
                'AMPERSAND' => new StringParser('&amp;'),              // ... or an escaped &
            ]
        );
    }
}
