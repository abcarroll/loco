<?php

use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Grammar;
use Ab\LocoX\Clause\Nonterminal\GreedyMultiParser;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\StringParser;

class Metagrammar extends Grammar
{
    public function __construct()
    {
        
        
        
        $internals = [
            'MultilineRule' => [
                new Sequence([
                    new RegexParser('/^[A-Z][A-Za-z0-9]*$/'),
                    '<nbsp>', new StringParser('::'), '<nbsp>', new StringParser("("), '<eol>',
                    new GreedyMultiParser('<$internal>', $lower, $upper)
            ]
        ];

        $S = 'File';
        $callback = null;

        parent::__construct($S, $internals, $callback);

    }
}
