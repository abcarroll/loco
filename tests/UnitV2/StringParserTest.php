<?php

namespace Ferno\Tests\Loco;

use Ab\LocoX\ParseFailureException;
use Ab\LocoX\StringParser;
use \PHPUnit\Framework\TestCase as TestCase;

class StringParserTest extends TestCase
{
    public function testMatchSuccess()
    {
        $parser = new StringParser("needle");
        $this->assertEquals(array("j" => 10, "value" => "needle"), $parser->match("asdfneedle", 4));
    }

    public function testMatchFailure()
    {
        $parser = new StringParser("needle");
        $this->expectException(ParseFailureException::_CLASS);
        $this->assertEquals(0, $parser->match("asdfneedle"));
    }
}
