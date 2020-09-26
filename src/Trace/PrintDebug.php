<?php

use Ab\LocoX\Exception\ParseFailureException;
use Ab\LocoX\Grammar;

class PrintDebug
{
    public function getDebugInfo(ParseFailureException $exception)
    {
        $data = substr($exception->inputStr, ($exception->failureOffset - 5), 32);
        $dataLine = str_replace(["\r", "\n"], ["_", "_"], $data);
        $subDataLine = "     ^^^^^ here\n";

        echo "\n$dataLine\n$subDataLine\n";
        die;
    }

    public function printParseTree(Grammar $g)
    {
        $g->
    }
}
