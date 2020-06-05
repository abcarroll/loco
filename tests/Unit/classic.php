<?php
/**
 * Created by PhpStorm.
 * User: mbrzuchalski
 * Date: 09.06.16
 * Time: 16:41
 */
namespace Ab\LocoX;

use Exception;

// unit tests
$test(function($assert) {
    // print("1A\n");
    $parser = new StringParser("needle");
    $assert($parser->match("asdfneedle", 4) === array("j" => 10, "value" => "needle"));
    try {
        $parser->match("asdfneedle", 0);
        $assert(false);
    } catch(ParseFailureException $e) {
        $assert(true);
    }
});

$test(function($assert) {
    // print("2\n");
    # improper anchoring
    try {
        $parser = new RegexParser("#boo#");
        $assert(false);
    } catch (GrammarException $e) {
        $assert(true);
    }
});

$test(function($assert) {
    // print("3A\n");
    $parser = new RegexParser("#^boo#");
    $assert($parser->match("boo", 0) === array("j" => 3, "value" => "boo"));
    try {
        $parser->match("aboo", 0);
        $assert(false);
    } catch(ParseFailureException $e) {
        $assert(true);
    }
    $assert($parser->match("aboo", 1) == array("j" => 4, "value" => "boo"));
    $parser = new RegexParser("#^-?(0|[1-9][0-9]*)(\.[0-9]*)?([eE][-+]?[0-9]*)?#");
    $assert($parser->match("-24.444E-009", 2) === array("j" => 12, "value" => "4.444E-009"));
});

$test(function($assert) {
    // print("5A\n");
    $parser = new Utf8Parser(array());

    try {
        $parser->match("", 0);
        $assert(false);
    } catch(ParseFailureException $e) {
        $assert(true);
    }

    $assert($parser->match("\x41",             0) === array("j" => 1, "value" => "\x41"            )); # 1-byte character "A"
    $assert($parser->match("\xC2\xAF",         0) === array("j" => 2, "value" => "\xC2\xAF"        )); # 2-byte character "¯"
    $assert($parser->match("\xE2\x99\xA5",     0) === array("j" => 3, "value" => "\xE2\x99\xA5"    )); # 3-byte character "♥"
    $assert($parser->match("\xF1\x8B\x81\x82", 0) === array("j" => 4, "value" => "\xF1\x8B\x81\x82")); # 4-byte character "񋁂"
    $assert($parser->match("\xEF\xBB\xBF",     0) === array("j" => 3, "value" => "\xEF\xBB\xBF"    )); # "byte order mark" 11101111 10111011 10111111 (U+FEFF)
    $assert($parser->match("\xF0\x90\x80\x80", 0) === array("j" => 4, "value" => "\xF0\x90\x80\x80")); # 4-byte character
    $assert($parser->match("\xF0\xA0\x80\x80", 0) === array("j" => 4, "value" => "\xF0\xA0\x80\x80")); # 4-byte character
    $assert($parser->match(
            "\x41\xC2\xAF\xE2\x99\xA5\xF1\x8B\x81\x82\xEF\xBB\xBF", 0) === array("j" => 1, "value" => "\x41")
    );

    try { $parser->match("\xF4\x90\x80\x80", 0); assert(false); } catch(ParseFailureException $e) { assert(true); } # code point U+110000, out of range (max is U+10FFFF)
    try { $parser->match("\xC0\xA6",         0); assert(false); } catch(ParseFailureException $e) { assert(true); } # overlong encoding (code point is U+26; should be 1 byte, "\x26")
    try { $parser->match("\xC3\xFF",         0); assert(false); } catch(ParseFailureException $e) { assert(true); } # illegal continuation byte
    try { $parser->match("\xFF",             0); assert(false); } catch(ParseFailureException $e) { assert(true); } # illegal leading byte
    try { $parser->match("\xC2",             0); assert(false); } catch(ParseFailureException $e) { assert(true); } # mid-character termination
    try { $parser->match("\x00",             0); assert(false); } catch(ParseFailureException $e) { assert(true); } # null
    try { $parser->match("\xED\xA0\x80",     0); assert(false); } catch(ParseFailureException $e) { assert(true); } # 55296d
    try { $parser->match("\xED\xBF\xBF",     0); assert(false); } catch(ParseFailureException $e) { assert(true); } # 57343d
});

$test(function($assert) {
    // print("5C\n");
    $assert(Utf8Parser::getBytes(0x41)    === "A");
    $assert(Utf8Parser::getBytes(0x26)    === "\x26");
    $assert(Utf8Parser::getBytes(0xAF)    === "\xC2\xAF");         # 2-byte character "¯"
    $assert(Utf8Parser::getBytes(0x2665)  === "\xE2\x99\xA5");     # 3-byte character "♥"
    $assert(Utf8Parser::getBytes(0xFEFF)  === "\xEF\xBB\xBF");     # "byte order mark" 11101111 10111011 10111111 (U+FEFF)
    $assert(Utf8Parser::getBytes(0x10000) === "\xF0\x90\x80\x80"); # 4-byte character
    $assert(Utf8Parser::getBytes(0x20000) === "\xF0\xA0\x80\x80"); # 4-byte character
    $assert(Utf8Parser::getBytes(0x4B042) === "\xF1\x8B\x81\x82"); # 4-byte character "񋁂"

    try {
        # invalid character in XML (not in any safe range)
        Utf8Parser::getBytes(0xD800);
        $assert(false);
    } catch (Exception $e) {
        $assert(true);
    }

    try {
        # code point too large, cannot be encoded
        Utf8Parser::getBytes(0xFFFFFF);
        $assert(false);
    } catch (Exception $e) {
        $assert(true);
    }
});

$test(function($assert) {
    // print("7A\n");
    $parser = new LazyAltParser(
        array(
            new StringParser("abc"),
            new StringParser("ab"),
            new StringParser("a")
        )
    );
    try {
        $parser->match("0", 1);
        $assert(false);
    } catch(ParseFailureException $e) {
        $assert(true);
    }
    $assert($parser->match("0a",    1) === array("j" => 2, "value" => "a"  ));
    $assert($parser->match("0ab",   1) === array("j" => 3, "value" => "ab" ));
    $assert($parser->match("0abc",  1) === array("j" => 4, "value" => "abc"));
    $assert($parser->match("0abcd", 1) === array("j" => 4, "value" => "abc"));

    // print("7B\n");
    try {
        new LazyAltParser(array());
        $assert(false);
    } catch(GrammarException $e) {
        $assert(true);
    }
});

$test(function($assert) {
    // print("8B\n");
    $parser = new ConcParser(
        array(
            new RegexParser("#^a*#"),
            new RegexParser("#^b+#"),
            new RegexParser("#^c*#")
        )
    );
    try {
        $parser->match("", 0);
        $assert(false);
    } catch(ParseFailureException $e) {
        $assert(true);
    }
    try {
        $parser->match("aaa", 0);
        $assert(false);
    } catch(ParseFailureException $e) {
        $assert(true);
    }
    $assert($parser->match("b",       0) === array("j" => 1, "value" => array("", "b", "")));
    $assert($parser->match("aaab",    0) === array("j" => 4, "value" => array("aaa", "b", "")));
    $assert($parser->match("aaabb",   0) === array("j" => 5, "value" => array("aaa", "bb", "")));
    $assert($parser->match("aaabbbc", 0) === array("j" => 7, "value" => array("aaa", "bbb", "c")));
});

$test(function($assert) {
    // print("10B\n");
    $parser = new GreedyMultiParser(
        new StringParser("a"), 0, null
    );
    $assert($parser->match("",    0) === array("j" => 0, "value" => array()));
    $assert($parser->match("a",   0) === array("j" => 1, "value" => array("a")));
    $assert($parser->match("aa",  0) === array("j" => 2, "value" => array("a", "a")));
    $assert($parser->match("aaa", 0) === array("j" => 3, "value" => array("a", "a", "a")));
});

// Test behaviour when given ambiguous inner parser
$test(function($assert) {
    // print("10C\n");
    $parser = new GreedyMultiParser(
        new LazyAltParser(
            array(
                new StringParser("ab"),
                new StringParser("a")
            )
        ),
        0,
        null
    );
    $assert($parser->match("",   0) === array("j" => 0, "value" => array()));
    $assert($parser->match("a",  0) === array("j" => 1, "value" => array("a")));
    $assert($parser->match("aa", 0) === array("j" => 2, "value" => array("a", "a")));
    $assert($parser->match("ab", 0) === array("j" => 2, "value" => array("ab")));
});

$test(function($assert) {
    // print("10D\n");
    $parser = new GreedyMultiParser(
        new LazyAltParser(
            array(
                new StringParser("aa"),
                new StringParser("a")
            )
        ),
        0,
        null
    );
    $assert($parser->match("",   0) === array("j" => 0, "value" => array()));
    $assert($parser->match("a",  0) === array("j" => 1, "value" => array("a")));
    $assert($parser->match("aa", 0) === array("j" => 2, "value" => array("aa")));
});

$test(function($assert) {
    // print("10E\n");
    $parser = new GreedyMultiParser(
        new StringParser("a"), 0, 1
    );
    $assert($parser->match("", 0) === array("j" => 0, "value" => array()));
    $assert($parser->match("a", 0) === array("j" => 1, "value" => array("a")));
});

$test(function($assert) {
    // print("10F\n");
    $parser = new GreedyMultiParser(new StringParser("f"), 0, 0);
    $assert($parser->match("", 0) === array("j" => 0, "value" => array()));
    $assert($parser->match("f", 0) === array("j" => 0, "value" => array()));
    $parser = new GreedyMultiParser(new StringParser("f"), 0, 1);
    $assert($parser->match("", 0) === array("j" => 0, "value" => array()));
    $assert($parser->match("f", 0) === array("j" => 1, "value" => array("f")));
    $assert($parser->match("ff", 0) === array("j" => 1, "value" => array("f")));
    $parser = new GreedyMultiParser(new StringParser("f"), 1, 2);
    try { $parser->match("", 0); assert(false); } catch(ParseFailureException $e) { assert(true); }
    $assert($parser->match("f", 0) === array("j" => 1, "value" => array("f")));
    $assert($parser->match("ff", 0) === array("j" => 2, "value" => array("f", "f")));
    $assert($parser->match("fff", 0) === array("j" => 2, "value" => array("f", "f")));
    $parser = new GreedyMultiParser(new StringParser("f"),	1, null);
    try { $parser->match("", 0); assert(false); } catch(ParseFailureException $e) { assert(true); }
    $assert($parser->match("f", 0) === array("j" => 1, "value" => array("f")));
    $assert($parser->match("ff", 0) === array("j" => 2, "value" => array("f", "f")));
    $assert($parser->match("fff", 0) === array("j" => 3, "value" => array("f", "f", "f")));
    $assert($parser->match("ffg", 0) === array("j" => 2, "value" => array("f", "f")));
});

// regular Grammar
$test(function($assert) {
    // print("11\n");
    $grammar = new Grammar(
        "<A>",
        array(
            "<A>" => new EmptyParser()
        )
    );
    try {
        $grammar->parse("a");
        $assert(false);
    } catch(ParseFailureException $e) {
        $assert(true);
    }
    $assert($grammar->parse("") === null);
});

// disallow GreedyMultiParsers with unbounded limits which can consume ""
$test(function($assert) {
    // print("12A\n");
    try {
        $grammar = new Grammar(
            "<S>",
            array(
                "<S>" => new GreedyMultiParser("<A>", 7, null),
                "<A>" => new EmptyParser()
            )
        );
        $assert(false);
    } catch(GrammarException $e) {
        $assert(true);
    }
    try {
        $grammar = new Grammar(
            "<S>",
            array(
                "<S>" => new GreedyStarParser("<A>"),
                "<A>" => new GreedyStarParser("<B>"),
                "<B>" => new EmptyParser()
            )
        );
        $assert(false);
    } catch(GrammarException $e) {
        $assert(true);
    }
});

// no parser for the root
$test(function($assert) {
    // print("13B\n");
    try {
        $grammar = new Grammar("<A>", array());
        $assert(false);
    } catch(GrammarException $e) {
        $assert(true);
    }
});

// left-recursion
$test(function($assert) {
    // print("13G\n");

    // obvious
    try {
        $grammar = new Grammar(
            "<S>",
            array(
                "<S>" => new ConcParser(array("<S>"))
            )
        );
        $assert(false);
    } catch (GrammarException $e) {
        $assert(true);
    }

    // more advanced (only left-recursive because <B> is nullable)
    try {
        $grammar = new Grammar(
            "<A>",
            array(
                "<A>" => new LazyAltParser(
                    array(
                        new StringParser("Y"),
                        new ConcParser(
                            array("<B>", "<A>")
                        )
                    )
                ),
                "<B>" => new EmptyParser()
            )
        );
        $assert(false);
    } catch (GrammarException $e) {
        $assert(true);
    }

    // Even more complex (this specifically checks for a bug in the
    // original Loco left-recursion check).
    // This grammar is left-recursive in A -> B -> D -> A
    try {
        $grammar = new Grammar(
            "<A>",
            array(
                "<A>" => new ConcParser(array("<B>")),
                "<B>" => new LazyAltParser(array("<C>", "<D>")),
                "<C>" => new ConcParser(array(new StringParser("C"))),
                "<D>" => new LazyAltParser(array("<C>", "<A>"))
            )
        );
        $assert(false);
    } catch (GrammarException $e) {
        $assert(true);
    }
});
