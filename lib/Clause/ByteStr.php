<?php

namespace Ab\LxExpression\Nonterminal;

class ByteStr
{
    private string $str;

    public function __construct(string $str)
    {
        $this->str = $str;
    }
}
