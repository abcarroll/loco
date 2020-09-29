<?php

namespace Ferno\Tests\Loco;

use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Clause\Nonterminal\UntilString;
use Ab\LocoX\Clause\RuleReference;
use Ab\LocoX\Clause\Terminal\EmptyParser;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\Utf8Parser;
use Ab\LocoX\Generate;
use Ab\LocoX\Exception\GrammarException;
use Ab\LocoX\Clause\Nonterminal\BoundedRepeat;
use Ab\LocoX\Clause\Nonterminal\GreedyStarParser;
use Ab\LocoX\Clause\Nonterminal\OrderedChoice;
use Ab\LocoX\Exception\ParseFailureException;
use Ab\LocoX\Clause\Terminal\StringParser;
use Ab\LocoX\Grammar;
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

        self::assertEquals(null, $grammar->parse(""));
    }

    public function testGreedyMultiParsersWIthUnboundedLimits()
    {
        $this->expectException(GrammarException::_CLASS);
        new Grammar(
            "<S>",
            array(
                "<S>" => new BoundedRepeat("<A>", 7, null),
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
                "<A>" => new OrderedChoice(
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
                "<B>" => new OrderedChoice(array("<C>", "<D>")),
                "<C>" => new Sequence(array(new StringParser("C"))),
                "<D>" => new OrderedChoice(array("<C>", "<A>"))
            )
        );
    }

    public function testRuleReferenceClassResolution()
    {
        $gg = new Grammar(
            'S',
            [
            'S' => new Sequence([
                new StringParser('abc')
            ]),
            'a' => new StringParser('xyz'),
            'b' => new Utf8Parser(['.']),
            'c' => new RegexParser('/^a+/'),
            'd' => new UntilString(['!']),
            'e' => new EmptyParser(),

            'x.1' => new Sequence(['a', 'b', 'c', 'd']),
            'x.2' => new OrderedChoice(['a', 'b', 'c', 'd', 'e']),
            'x.3' => new BoundedRepeat(new Sequence(['x.1']), 0, 1),
            'x.4' => new GreedyStarParser(new Sequence(['x.1', 'x.2'])),

            'y.1' => new Sequence([new RuleReference('a'), new RuleReference('b'), new RuleReference('c'), new RuleReference('d')]),
            'y.2' => new OrderedChoice([new RuleReference('a'), new RuleReference('b'), new RuleReference('c'), new RuleReference('d'), new RuleReference('e')]),
            'y.3' => new BoundedRepeat(new Sequence([new RuleReference('x.1')]), 0, 1),
            'y.4' => new GreedyStarParser(new Sequence([new RuleReference('x.1'), new RuleReference('x.2')])),

            ]
        );
    }
}
