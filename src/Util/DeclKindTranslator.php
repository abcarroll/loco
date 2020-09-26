<?php

use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Grammar;
use Ab\LocoX\MonoParser;

class DeclKindTranslator
{
    private Grammar $grammar;

    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    private static function getStringRepresentation(string $string)
    {
        return '"' . addcslashes($string, '"') . '"';
    }

    public function getFunctionalDeclaration()
    {
        $fnDeclString = "";
        foreach($this->grammar->internals as $ruleName => $internal) {
            /** @var MonoParser $internal */
            $fnDeclString .= "rule(" . self::getStringRepresentation($ruleName) . ", ";
            switch(get_class($internal)) {
                case Sequence::class:
                    $fnDeclString .= "seq(";
                    foreach($internal->internals as $subinternal) {

                    }
            }
        }
    }
}
