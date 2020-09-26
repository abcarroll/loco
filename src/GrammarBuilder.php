<?php

namespace Ab\LocoX;

class GrammarBuilder
{
    private array $rules = [];

    public function addRule(string $ruleName, Clause $clause)
    {
        $rules[] = [$ruleName, $clause];
    }

    public function addPrecedenceRule(string $ruleName, Clause $clause, int $precedence, int $assoc)
    {

    }

    public function mergeGrammar(string $ruleName, Grammar $clause, bool $duplicateOverwrite = true)
    {
        $rules[] = [$ruleName, $clause, $duplicateOverwrite];
    }

    public function getSerialized()
    {

    }
}
