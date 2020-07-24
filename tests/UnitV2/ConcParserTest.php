<?php

namespace Ferno\Tests\Loco;

use Ab\LocoX\ConcParser;
use Ab\LocoX\ParseFailureException;
use Ab\LocoX\RegexParser;
use PHPUnit\Framework\TestCase;

class ConcParserTest extends TestCase
{
    /** @var ConcParser */
    private $parser;

    public function setUp(): void
    {
        $this->parser = new ConcParser(
            array(
                new RegexParser("#^a*#"),
                new RegexParser("#^b+#"),
                new RegexParser("#^c*#")
            )
        );
    }

    public function testEmptyFails()
    {
        $this->expectException(ParseFailureException::_CLASS);
        $this->parser->match("", 0);
    }

    public function testNonConsecutiveFails()
    {
        $this->expectException(ParseFailureException::_CLASS);
        $this->parser->match("aaa", 0);
    }

    public function testSuccessCases()
    {
        $this->assertEquals(array("j" => 1, "value" => array("", "b", "")), $this->parser->match("b", 0));
        $this->assertEquals(array("j" => 4, "value" => array("aaa", "b", "")), $this->parser->match("aaab", 0));
        $this->assertEquals(array("j" => 5, "value" => array("aaa", "bb", "")), $this->parser->match("aaabb", 0));
        $this->assertEquals(array("j" => 7, "value" => array("aaa", "bbb", "c")), $this->parser->match("aaabbbc", 0));
    }
}
