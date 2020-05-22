<?php


namespace Ferno\Loco\grammar;

use Ferno\Loco\ConcParser;
use Ferno\Loco\Grammar;
use Ferno\Loco\GreedyStarParser;
use Ferno\Loco\LazyAltParser;
use Ferno\Loco\RegexParser;
use Ferno\Loco\StringParser;
use Ferno\Loco\Utf8Parser;

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
                '<blockorwhitespace>' => new LazyAltParser(
                    ['<h5>', '<p>', 'WHITESPACE']
                ),
                '<p>' => new ConcParser(
                    ['OPEN_P', '<text>', 'CLOSE_P'],
                    function ($open_p, $text, $close_p) { return $open_p . $text . $close_p; }
                ),
                '<h5>' => new ConcParser(
                    ['OPEN_H5', '<text>', 'CLOSE_H5'],
                    function ($open_h5, $text, $close_h5) { return $open_h5 . $text . $close_h5; }
                ),
                '<strong>' => new ConcParser(
                    ['OPEN_STRONG', '<text>', 'CLOSE_STRONG'],
                    function ($open_strong, $text, $close_strong) { return $open_strong . $text . $close_strong; }
                ),
                '<em>' => new ConcParser(
                    ['OPEN_EM', '<text>', 'CLOSE_EM'],
                    function ($open_em, $text, $close_em) { return $open_em . $text . $close_em; }
                ),
                '<text>' => new GreedyStarParser(
                    '<atom>',
                    function () { return implode('', func_get_args()); }
                ),
                '<atom>' => new LazyAltParser(
                    ['<char>', '<strong>', '<em>', 'FULL_BR']
                ),
                '<char>' => new LazyAltParser(
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
