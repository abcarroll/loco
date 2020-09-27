<?php


namespace Ab\LocoX\Grammar;

use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Clause\Terminal\EmptyParser;
use Ab\LocoX\Grammar;
use Ab\LocoX\Clause\Nonterminal\GreedyStarParser;
use Ab\LocoX\Clause\Nonterminal\OrderedChoice;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\StringParser;
use Ab\LocoX\Clause\Terminal\Utf8Parser;

class JsonGrammar extends Grammar
{
    public function __construct()
    {
        parent::__construct(
            '<topobject>',
            [
                '<topobject>' => new Sequence(
                    ['WHITESPACE', '<object>'],
                    function ($whitespace, $object) { return $object; }
                ),

                '<object>' => new Sequence(
                    ['LEFT_BRACE', 'WHITESPACE', '<objectcontent>', 'RIGHT_BRACE', 'WHITESPACE'],
                    function ($left_brace, $whitespace1, $objectcontent, $right_brace, $whitespace2) { return $objectcontent; }
                ),

                '<objectcontent>' => new OrderedChoice(
                    ['<fullobject>', '<emptyobject>']
                ),

                '<fullobject>' => new Sequence(
                    ['<keyvalue>', '<commakeyvaluelist>'],
                    function ($keyvalue, $commakeyvaluelist) {
                        $commakeyvaluelist[$keyvalue[0]] = $keyvalue[1];

                        return $commakeyvaluelist;
                    }
                ),

                '<emptyobject>' => new EmptyParser(
                    function () { return []; }
                ),

                '<commakeyvaluelist>' => new GreedyStarParser(
                    '<commakeyvalue>',
                    function () {
                        $commakeyvaluelist = [];
                        foreach (func_get_args() as $commakeyvalue) {
                            $commakeyvaluelist[$commakeyvalue[0]] = $commakeyvalue[1];
                        }

                        return $commakeyvaluelist;
                    }
                ),

                '<commakeyvalue>' => new Sequence(
                    ['COMMA', 'WHITESPACE', '<keyvalue>'],
                    function ($comma, $whitespace, $keyvalue) { return $keyvalue; }
                ),

                '<keyvalue>' => new Sequence(
                    ['<string>', 'COLON', 'WHITESPACE', '<value>'],
                    function ($string, $colon, $whitespace, $value) { return [$string, $value]; }
                ),

                '<array>' => new Sequence(
                    ['LEFT_BRACKET', 'WHITESPACE', '<arraycontent>', 'RIGHT_BRACKET', 'WHITESPACE'],
                    function ($left_bracket, $whitespace1, $arraycontent, $right_bracket, $whitespace2) { return $arraycontent; }
                ),

                '<arraycontent>' => new OrderedChoice(
                    ['<fullarray>', '<emptyarray>']
                ),

                '<fullarray>' => new Sequence(
                    ['<value>', '<commavaluelist>'],
                    function ($value, $commavaluelist) {
                        array_unshift($commavaluelist, $value);

                        return $commavaluelist;
                    }
                ),

                '<emptyarray>' => new EmptyParser(
                    function () { return []; }
                ),

                '<commavaluelist>' => new GreedyStarParser('<commavalue>'),

                '<commavalue>' => new Sequence(
                    ['COMMA', 'WHITESPACE', '<value>'],
                    function ($comma, $whitespace, $value) { return $value; }
                ),

                '<value>' => new OrderedChoice(
                    ['<string>', '<number>', '<object>', '<array>', '<true>', '<false>', '<null>']
                ),

                '<string>' => new Sequence(
                    ['DOUBLE_QUOTE', '<stringcontent>', 'DOUBLE_QUOTE', 'WHITESPACE'],
                    function ($double_quote1, $stringcontent, $double_quote2, $whitespace) { return $stringcontent; }
                ),

                '<stringcontent>' => new GreedyStarParser(
                    '<char>',
                    function () { return implode('', func_get_args()); }
                ),

                '<char>' => new OrderedChoice(
                    [
                        'UTF8_EXCEPT', 'ESCAPED_QUOTE', 'ESCAPED_BACKSLASH', 'ESCAPED_SLASH', 'ESCAPED_B',
                        'ESCAPED_F', 'ESCAPED_N', 'ESCAPED_R', 'ESCAPED_T', 'ESCAPED_UTF8'
                    ]
                ),

                '<number>' => new Sequence(['NUMBER', 'WHITESPACE'], function ($number, $whitespace) { return $number; }),
                '<true>' => new Sequence(['TRUE',   'WHITESPACE'], function ($true, $whitespace) { return true; }),
                '<false>' => new Sequence(['FALSE',  'WHITESPACE'], function ($false, $whitespace) { return false; }),
                '<null>' => new Sequence(['NULL',   'WHITESPACE'], function ($null, $whitespace) {  }),

                // actual physical objects (RegexParsers, StringParsers and Utf8Parsers)
                // are represented in all capitals because they are important.
                // this is effectively the lexer portion of the whole shebang.

                'WHITESPACE' => new  RegexParser("#^[ \n\r\t]*#"), // ignored
                'LEFT_BRACE' => new StringParser('{'),             // ignored
                'RIGHT_BRACE' => new StringParser('}'),             // ignored
                'LEFT_BRACKET' => new StringParser('['),             // ignored
                'RIGHT_BRACKET' => new StringParser(']'),             // ignored
                'COLON' => new StringParser(':'),             // ignored
                'COMMA' => new StringParser(','),             // ignored
                'DOUBLE_QUOTE' => new StringParser('"'),            // ignored

                'NUMBER' => new  RegexParser('#^-?(0|[1-9][0-9]*)(\.[0-9]+)?([eE][-+]?[0-9]+)?#', function ($match) { return (float) $match; }),
                'TRUE' => new StringParser('true'),
                'FALSE' => new StringParser('false'),
                'NULL' => new StringParser('null'),

                // "Any UNICODE character except..."
                'UTF8_EXCEPT' => new Utf8Parser(
                    array_merge(
                    // "double quote or backslash..."
                        ['"', '\\'],

                        // "or control character"
                        array_map(
                            function ($codepoint) {
                                return Utf8Parser::getBytes($codepoint);
                            },
                            Utf8Parser::$controls
                        )
                    )
                ),
                'ESCAPED_QUOTE' => new StringParser('\\"', function ($string) { return substr($string, 1, 1); }),
                'ESCAPED_BACKSLASH' => new StringParser('\\\\', function ($string) { return substr($string, 1, 1); }),
                'ESCAPED_SLASH' => new StringParser('\\/', function ($string) { return substr($string, 1, 1); }),
                'ESCAPED_B' => new StringParser('\\b', function ($string) { return "\b"; }),
                'ESCAPED_F' => new StringParser('\\f', function ($string) { return "\f"; }),
                'ESCAPED_N' => new StringParser('\\n', function ($string) { return "\n"; }),
                'ESCAPED_R' => new StringParser('\\r', function ($string) { return "\r"; }),
                'ESCAPED_T' => new StringParser('\\t', function ($string) { return "\t"; }),
                'ESCAPED_UTF8' => new  RegexParser('#^\\\\u[0-9a-fA-F]{4}#', function ($match) { return Utf8Parser::getBytes(hexdec(substr($match, 2, 4))); })
            ]
        );
    }
}
