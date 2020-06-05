<?php

use PHPUnit\Framework\TestBuilder;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;
use PHPUnit\Framework\TestCase;

class CallableTests extends TestCase
{
    private $queuedCallables = [];

    public function setUp(): void
    {
        $test = function($callable) {
            $assertFn = function ($assertion)  {
                $dbg = debug_backtrace(false);
                $caller = $dbg[0]['file'] . ':' . $dbg[0]['line'];

                self::assertTrue($assertion, $caller);
            };

            $this->queuedCallables[] = [$callable, $assertFn];
        };

        require __DIR__ . '/Unit/classic.php';
    }

    /**
     * @test
     */
    public function runAllClassicTests()
    {
        foreach ($this->queuedCallables as $id => $calls) {
            [$callable, $assert] = $calls;
            $callable($assert);
        }
    }

    /*
    $assertion = (bool) $assertion;
    TestCase::assertTrue($assertion, $caller);
    */
}

//require __DIR__ . '/../docs/examples/bnf.php';
//require __DIR__ . '/../docs/examples/ebnf.php';
//require __DIR__ . '/../docs/examples/json.php';
//require __DIR__ . '/../docs/examples/left.php';
//require __DIR__ . '/../docs/examples/locoNotation.php';
