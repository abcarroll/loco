<?php

namespace Ferno\Tests\Loco;

use Ab\LocoX\Clause\ClauseFactory;
use Ab\LocoX\GrammarException;
use Ab\LocoX\Exception\ParseFailureException;
use Ab\LocoX\Clause\Nonterminal\LazyAltParser;
use Ab\LocoX\Clause\Terminal\StringParser;
use PHPUnit\Framework\TestCase;

class MatchFactoryTest extends TestCase
{
    public function testMatchCharsOfString()
    {
        $parser = ClauseFactory::matchCharOfString('abc');
        $result = $parser->match('cba');
        self::assertEquals(['j' => 3, 'value' => 'cba'], $result);
    }

    /**
     * Ambiguous, so just fail.
     */
    public function testMatchCharsOfStringEmptyFailure()
    {
        $this->expectException(\InvalidArgumentException::class);
        ClauseFactory::matchCharOfString('');
    }
}
