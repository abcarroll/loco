<?php

namespace Ab\LocoX;

class Rule implements \JsonSerializable
{
    private string $label;
    private Clause $clause;

    public function __construct(string $label, Clause $clause)
    {
        $this->label = $label;
        $this->clause = $clause;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getClause()
    {
        return $this->clause;
    }

    public function jsonSerialize()
    {
        return [
            'rule' => [
                'label' => $this->getLabel(),
                'clause' => $this->getClause()->jsonSerialize()
            ]
        ];
    }

    public function toStringGrammar()
    {
        return $this->getLabel() . " <- (" . $this->getClause()->toStringGrammar() . ");\n";
    }
}
