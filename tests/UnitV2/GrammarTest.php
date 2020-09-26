<?php

namespace Ferno\Tests\Loco;

use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Clause\Terminal\EmptyParser;
use Ab\LocoX\Grammar;
use Ab\LocoX\GrammarException;
use Ab\LocoX\Clause\Nonterminal\GreedyMultiParser;
use Ab\LocoX\Clause\Nonterminal\GreedyStarParser;
use Ab\LocoX\Clause\Nonterminal\LazyAltParser;
use Ab\LocoX\Exception\ParseFailureException;
use Ab\LocoX\Clause\Terminal\StringParser;
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
                "<S>" => new Sequence(array("<S>"))
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
                        new Sequence(
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
                "<A>" => new Sequence(array("<B>")),
                "<B>" => new LazyAltParser(array("<C>", "<D>")),
                "<C>" => new Sequence(array(new StringParser("C"))),
                "<D>" => new LazyAltParser(array("<C>", "<A>"))
            )
        );
    }
}
