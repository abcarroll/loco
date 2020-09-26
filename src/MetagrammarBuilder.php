<?php

use Ab\LocoX\Clause\Terminal\RegexParser;

class StandardTokens
{
    public static function getNonBreakingWhitespace(): Clause
    {
        return new RegexParser('/^[ \t]+/');

    }
}

class MetagrammarBuilder
{
    public const ACTION_OPTIONAL = ':optional';
    public const ACTION_IGNORE = '';

    private $implicitToken = null;

    public function setImplicitToken(string $implicitTokenName)
    {

    }

    public function ($left, $right, $action)
    {

    }
}

$mgb = new MetagrammarBuilder();
$mgb->addGrouping('[', ']', MetagrammarBuilder::);
