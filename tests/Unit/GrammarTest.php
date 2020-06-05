<?php

namespace Ferno\Tests\Loco;

use Ab\LocoX\ConcParser;
use Ab\LocoX\EmptyParser;
use Ab\LocoX\Grammar;
use Ab\LocoX\GrammarException;
use Ab\LocoX\GreedyMultiParser;
use Ab\LocoX\GreedyStarParser;
use Ab\LocoX\LazyAltParser;
use Ab\LocoX\ParseFailureException;
use Ab\LocoX\StringParser;
use \PHPUnit\Framework\TestCase as TestCase;

class GrammarTest extends TestCase
{
    public function testMatchSimpleFailure()
    {
        $grammar = new Grammar(
            "<A>",
            array(
                "<A>" => new EmptyParser()
            )
        );

        $this->expectException(ParseFailureException::_CLASS);
        $grammar->parse("a");
    }

    public function testMatchSimpleSuccess()
    {
        $grammar = new Grammar(
            "<A>",
            array(
                "<A>" => new EmptyParser()
            )
        );

        $this->assertEquals(null, $grammar->parse(""));
    }

    public function testGreedyMultiParsersWIthUnboundedLimits()
    {
        $this->expectException(GrammarException::_CLASS);
        new Grammar(
            "<S>",
            array(
                "<S>" => new GreedyMultiParser("<A>", 7, null),
                "<A>" => new EmptyParser()
            )
        );
    }

    public function testGreedyStarParsersWIthUnboundedLimits()
    {
        $this->expectException(GrammarException::_CLASS);
        new Grammar(
            "<S>",
            array(
                "<S>" => new GreedyStarParser("<A>"),
                "<A>" => new GreedyStarParser("<B>"),
                "<B>" => new EmptyParser()
            )
        );
    }

    public function testNoRootParser()
    {
        $this->expectException(GrammarException::_CLASS);
        new Grammar("<A>", array());
    }

    public function testSimpleLeftRecursion()
    {
        $this->expectException(GrammarException::_CLASS);
        new Grammar(
            "<S>",
            array(
                "<S>" => new ConcParser(array("<S>"))
            )
        );
    }

    public function testAdvancedLeftRecursion()
    {
        // more advanced (only left-recursive because <B> is nullable)

        $this->expectException(GrammarException::_CLASS);
        new Grammar(
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
    }

    public function testLongRecursionChains()
    {
        // Even more complex (this specifically checks for a bug in the
        // original Loco left-recursion check).
        // This grammar is left-recursive in A -> B -> D -> A

        $this->expectException(GrammarException::_CLASS);
        new Grammar(
            "<A>",
            array(
                "<A>" => new ConcParser(array("<B>")),
                "<B>" => new LazyAltParser(array("<C>", "<D>")),
                "<C>" => new ConcParser(array(new StringParser("C"))),
                "<D>" => new LazyAltParser(array("<C>", "<A>"))
            )
        );
    }
}
