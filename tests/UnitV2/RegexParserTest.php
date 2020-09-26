<?php

namespace Ferno\Tests\Loco;

use Ab\LocoX\GrammarException;
use Ab\LocoX\Exception\ParseFailureException;
use Ab\LocoX\Clause\Terminal\RegexParser;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use \PHPUnit\Framework\TestCase as TestCase;

class RegexParserTest extends TestCase
{
    public function testImproperAnchoring()
    {
        $this->expectException(GrammarException::_CLASS);
        new RegexParser("#boo#");
    }

    public function testNonMatching()
    {
        $parser = new RegexParser("#^boo#");
        $this->expectException(ParseFailureException::_CLASS);
        $parser->match("aboo", 0);
    }

    public function testMatching()
    {
        $parser = new RegexParser("#^boo#");

        $this->assertEquals(array("j" => 4, "value" => "boo"), $parser->match("aboo", 1));
    }

    public function testMatchingNumeric()
    {
        $parser = new RegexParser("#^-?(0|[1-9][0-9]*)(\\.[0-9]*)?([eE][-+]?[0-9]*)?#");
        $this->assertEquals(array("j" => 12, "value" => "4.444E-009"), $parser->match("-24.444E-009", 2));
    }

    public function testNormalizeDelimiter()
    {

    }
}
