<?php

namespace Ab\LocoX\Clause\Nonterminal;

use Ab\LocoX\Clause;


class Not extends Clause
{
    private Parser $subclause;

    public function __construct(Clause $parser)
    {
        $this->parser = $parser;
    }

    public function firstSet()
    {
        return new self($this->parser->firstSet());
    }

    public function evaluateNullability()
    {
        // TODO: Implement evaluateNullability() method.
    }

    public function match($string, $i = 0)
    {
        // TODO: Implement match() method.
    }

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}
