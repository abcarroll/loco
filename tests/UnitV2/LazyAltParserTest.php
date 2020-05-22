<?php

namespace Ferno\Tests\Loco;

use Ferno\Loco\GrammarException;
use Ferno\Loco\ParseFailureException;
use Ferno\Loco\LazyAltParser;
use Ferno\Loco\StringParser;
use PHPUnit\Framework\TestCase;

class LazyAltParserTest extends TestCase
{
    /** @var StringParser */
    private $parser;

    public function setUp(): void
    {
        $this->parser = new LazyAltParser(
            array(
                new StringParser("abc"),
                new StringParser("ab"),
                new StringParser("a")
            )
        );
    }

    public function testNonMatchingString()
    {
        $this->expectException(ParseFailureException::_CLASS);
        $this->parser->match('0', 1);
    }

    public function testMatchingStrings()
    {
        $this->assertEquals(array("j" => 2, "value" => "a"), $this->parser->match("0a", 1));
        $this->assertEquals(array("j" => 3, "value" => "ab"), $this->parser->match("0ab", 1));
        $this->assertEquals(array("j" => 4, "value" => "abc"), $this->parser->match("0abc", 1));
        $this->assertEquals(array("j" => 4, "value" => "abc"), $this->parser->match("0abcd", 1));
    }

    public function testEmptyParser()
    {
        $this->expectException(GrammarException::_CLASS);
        new LazyAltParser(array());
    }
}
