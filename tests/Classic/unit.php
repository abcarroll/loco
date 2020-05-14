<?php
/**
 * Original "Unit" Tests in all their glory.
 *
 * These tests are kept as-is, VERBATIM, ... in order to ensure that the parsers behave just as they did in the previous
 * library.  The only changes that have been done to them is changing assert() to assert() and wrapping them in
 * closures for easier test checking.
 *
 * @author Originally by 'mbrzuchalski'
 */

namespace Ab\LocoX\Tests\Classic;

use \Ab\LocoX\{
    StringParser,
    EmptyParser,
    ConcParser,
    Grammar,
    RegexParser,
    GreedyMultiParser,
    GreedyStarParser,
    Utf8Parser,
    Exception\GrammarException,
    Exception\ParseFailureException
};
use \Exception, \Closure;
use function \assert;

/**
 * @var \Closure $test
 * @type \Closure<\Closure> $test
 */

// unit tests
$test(function () {
    echo "1A\n";
    $parser = new StringParser('needle');
    \assert($parser->match('asdfneedle', 4) === ['j' => 10, 'value' => 'needle']);

    try {
        $parser->match('asdfneedle', 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }
});

$test(function () {
    echo "2\n";
    # improper anchoring
    try {
        $parser = new RegexParser('#boo#');
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }
});

$test(function () {
    echo "3A\n";
    $parser = new RegexParser('#^boo#');
    \assert($parser->match('boo', 0) === ['j' => 3, 'value' => 'boo']);

    try {
        $parser->match('aboo', 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }
    \assert($parser->match('aboo', 1) === ['j' => 4, 'value' => 'boo']);
    $parser = new RegexParser("#^-?(0|[1-9][0-9]*)(\.[0-9]*)?([eE][-+]?[0-9]*)?#");
    \assert($parser->match('-24.444E-009', 2) === ['j' => 12, 'value' => '4.444E-009']);
});

$test(function () {
    echo "5A\n";
    $parser = new Utf8Parser([]);

    try {
        $parser->match('', 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }

    \assert($parser->match("\x41", 0) === ['j' => 1, 'value' => "\x41"]); # 1-byte character "A"
    \assert($parser->match("\xC2\xAF", 0) === ['j' => 2, 'value' => "\xC2\xAF"]); # 2-byte character "¯"
    \assert($parser->match("\xE2\x99\xA5", 0) === ['j' => 3, 'value' => "\xE2\x99\xA5"]); # 3-byte character "♥"
    \assert($parser->match("\xF1\x8B\x81\x82", 0) === ['j' => 4, 'value' => "\xF1\x8B\x81\x82"]); # 4-byte character "񋁂"
    \assert($parser->match("\xEF\xBB\xBF", 0) === ['j' => 3, 'value' => "\xEF\xBB\xBF"]); # "byte order mark" 11101111 10111011 10111111 (U+FEFF)
    \assert($parser->match("\xF0\x90\x80\x80", 0) === ['j' => 4, 'value' => "\xF0\x90\x80\x80"]); # 4-byte character
    \assert($parser->match("\xF0\xA0\x80\x80", 0) === ['j' => 4, 'value' => "\xF0\xA0\x80\x80"]); # 4-byte character
    \assert($parser->match(
        "\x41\xC2\xAF\xE2\x99\xA5\xF1\x8B\x81\x82\xEF\xBB\xBF",
        0
    ) === ['j' => 1, 'value' => "\x41"]);

    try {
        $parser->match("\xF4\x90\x80\x80", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # code point U+110000, out of range (max is U+10FFFF)
    try {
        $parser->match("\xC0\xA6", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # overlong encoding (code point is U+26; should be 1 byte, "\x26")
    try {
        $parser->match("\xC3\xFF", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # illegal continuation byte
    try {
        $parser->match("\xFF", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # illegal leading byte
    try {
        $parser->match("\xC2", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # mid-character termination
    try {
        $parser->match("\x00", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # null
    try {
        $parser->match("\xED\xA0\x80", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # 55296d
    try {
        $parser->match("\xED\xBF\xBF", 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    } # 57343d
});

$test(function () {
    echo "5C\n";
    \assert('A' === Utf8Parser::getBytes(0x41));
    \assert("\x26" === Utf8Parser::getBytes(0x26));
    \assert("\xC2\xAF" === Utf8Parser::getBytes(0xAF));         # 2-byte character "¯"
    \assert("\xE2\x99\xA5" === Utf8Parser::getBytes(0x2665));     # 3-byte character "♥"
    \assert("\xEF\xBB\xBF" === Utf8Parser::getBytes(0xFEFF));     # "byte order mark" 11101111 10111011 10111111 (U+FEFF)
    \assert("\xF0\x90\x80\x80" === Utf8Parser::getBytes(0x10000)); # 4-byte character
    \assert("\xF0\xA0\x80\x80" === Utf8Parser::getBytes(0x20000)); # 4-byte character
    \assert("\xF1\x8B\x81\x82" === Utf8Parser::getBytes(0x4B042)); # 4-byte character "񋁂"

    try {
        # invalid character in XML (not in any safe range)
        Utf8Parser::getBytes(0xD800);
        \assert(false);
    } catch (Exception $e) {
        \assert(true);
    }

    try {
        # code point too large, cannot be encoded
        Utf8Parser::getBytes(0xFFFFFF);
        \assert(false);
    } catch (Exception $e) {
        \assert(true);
    }
});

$test(function () {
    echo "7A\n";
    $parser = new LazyAltParser(
        [
            new StringParser('abc'),
            new StringParser('ab'),
            new StringParser('a')
        ]
    );

    try {
        $parser->match('0', 1);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }
    \assert($parser->match('0a', 1) === ['j' => 2, 'value' => 'a']);
    \assert($parser->match('0ab', 1) === ['j' => 3, 'value' => 'ab']);
    \assert($parser->match('0abc', 1) === ['j' => 4, 'value' => 'abc']);
    \assert($parser->match('0abcd', 1) === ['j' => 4, 'value' => 'abc']);

    echo "7B\n";

    try {
        new LazyAltParser([]);
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }
});

$test(function () {
    echo "8B\n";
    $parser = new ConcParser(
        [
            new RegexParser('#^a*#'),
            new RegexParser('#^b+#'),
            new RegexParser('#^c*#')
        ]
    );

    try {
        $parser->match('', 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }

    try {
        $parser->match('aaa', 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }
    \assert($parser->match('b', 0) === ['j' => 1, 'value' => ['', 'b', '']]);
    \assert($parser->match('aaab', 0) === ['j' => 4, 'value' => ['aaa', 'b', '']]);
    \assert($parser->match('aaabb', 0) === ['j' => 5, 'value' => ['aaa', 'bb', '']]);
    \assert($parser->match('aaabbbc', 0) === ['j' => 7, 'value' => ['aaa', 'bbb', 'c']]);
});

$test(function () {
    echo "10B\n";
    $parser = new GreedyMultiParser(
        new StringParser('a'),
        0,
        null
    );
    \assert($parser->match('', 0) === ['j' => 0, 'value' => []]);
    \assert($parser->match('a', 0) === ['j' => 1, 'value' => ['a']]);
    \assert($parser->match('aa', 0) === ['j' => 2, 'value' => ['a', 'a']]);
    \assert($parser->match('aaa', 0) === ['j' => 3, 'value' => ['a', 'a', 'a']]);
});

// Test behaviour when given ambiguous inner parser
$test(function () {
    echo "10C\n";
    $parser = new GreedyMultiParser(
        new LazyAltParser(
            [
                new StringParser('ab'),
                new StringParser('a')
            ]
        ),
        0,
        null
    );
    \assert($parser->match('', 0) === ['j' => 0, 'value' => []]);
    \assert($parser->match('a', 0) === ['j' => 1, 'value' => ['a']]);
    \assert($parser->match('aa', 0) === ['j' => 2, 'value' => ['a', 'a']]);
    \assert($parser->match('ab', 0) === ['j' => 2, 'value' => ['ab']]);
});

$test(function () {
    echo "10D\n";
    $parser = new GreedyMultiParser(
        new LazyAltParser(
            [
                new StringParser('aa'),
                new StringParser('a')
            ]
        ),
        0,
        null
    );
    \assert($parser->match('', 0) === ['j' => 0, 'value' => []]);
    \assert($parser->match('a', 0) === ['j' => 1, 'value' => ['a']]);
    \assert($parser->match('aa', 0) === ['j' => 2, 'value' => ['aa']]);
});

$test(function () {
    echo "10E\n";
    $parser = new GreedyMultiParser(
        new StringParser('a'),
        0,
        1
    );
    \assert($parser->match('', 0) === ['j' => 0, 'value' => []]);
    \assert($parser->match('a', 0) === ['j' => 1, 'value' => ['a']]);
});

$test(function () {
    echo "10F\n";
    $parser = new GreedyMultiParser(new StringParser('f'), 0, 0);
    \assert($parser->match('', 0) === ['j' => 0, 'value' => []]);
    \assert($parser->match('f', 0) === ['j' => 0, 'value' => []]);
    $parser = new GreedyMultiParser(new StringParser('f'), 0, 1);
    \assert($parser->match('', 0) === ['j' => 0, 'value' => []]);
    \assert($parser->match('f', 0) === ['j' => 1, 'value' => ['f']]);
    \assert($parser->match('ff', 0) === ['j' => 1, 'value' => ['f']]);
    $parser = new GreedyMultiParser(new StringParser('f'), 1, 2);

    try {
        $parser->match('', 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }
    \assert($parser->match('f', 0) === ['j' => 1, 'value' => ['f']]);
    \assert($parser->match('ff', 0) === ['j' => 2, 'value' => ['f', 'f']]);
    \assert($parser->match('fff', 0) === ['j' => 2, 'value' => ['f', 'f']]);
    $parser = new GreedyMultiParser(new StringParser('f'), 1, null);

    try {
        $parser->match('', 0);
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }
    \assert($parser->match('f', 0) === ['j' => 1, 'value' => ['f']]);
    \assert($parser->match('ff', 0) === ['j' => 2, 'value' => ['f', 'f']]);
    \assert($parser->match('fff', 0) === ['j' => 3, 'value' => ['f', 'f', 'f']]);
    \assert($parser->match('ffg', 0) === ['j' => 2, 'value' => ['f', 'f']]);
});

// regular Grammar
$test(function () {
    echo "11\n";
    $grammar = new Grammar(
        '<A>',
        [
            '<A>' => new EmptyParser()
        ]
    );

    try {
        $grammar->parse('a');
        \assert(false);
    } catch (ParseFailureException $e) {
        \assert(true);
    }
    \assert(null === $grammar->parse(''));
});

// disallow GreedyMultiParsers with unbounded limits which can consume ""
$test(function () {
    echo "12A\n";

    try {
        $grammar = new Grammar(
            '<S>',
            [
                '<S>' => new GreedyMultiParser('<A>', 7, null),
                '<A>' => new EmptyParser()
            ]
        );
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }

    try {
        $grammar = new Grammar(
            '<S>',
            [
                '<S>' => new GreedyStarParser('<A>'),
                '<A>' => new GreedyStarParser('<B>'),
                '<B>' => new EmptyParser()
            ]
        );
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }
});

// no parser for the root
$test(function () {
    echo "13B\n";

    try {
        $grammar = new Grammar('<A>', []);
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }
});

// left-recursion
$test(function () {
    echo "13G\n";

    // obvious
    try {
        $grammar = new Grammar(
            '<S>',
            [
                '<S>' => new ConcParser(['<S>'])
            ]
        );
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }

    // more advanced (only left-recursive because <B> is nullable)
    try {
        $grammar = new Grammar(
            '<A>',
            [
                '<A>' => new LazyAltParser(
                    [
                        new StringParser('Y'),
                        new ConcParser(
                            ['<B>', '<A>']
                        )
                    ]
                ),
                '<B>' => new EmptyParser()
            ]
        );
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }

    // Even more complex (this specifically checks for a bug in the
    // original Loco left-recursion check).
    // This grammar is left-recursive in A -> B -> D -> A
    try {
        $grammar = new Grammar(
            '<A>',
            [
                '<A>' => new ConcParser(['<B>']),
                '<B>' => new LazyAltParser(['<C>', '<D>']),
                '<C>' => new ConcParser([new StringParser('C')]),
                '<D>' => new LazyAltParser(['<C>', '<A>'])
            ]
        );
        \assert(false);
    } catch (GrammarException $e) {
        \assert(true);
    }
});
