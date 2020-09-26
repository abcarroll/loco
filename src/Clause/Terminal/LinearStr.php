<?php

namespace Ab\LocoX\Clause\Terminal;


class LinearStr
{
    private string $assertNext;

    public function __construct(string $assertNext)
    {
        $this->assertNext = $assertNext;
    }

    public function getResult($string, int $i = 0)
    {
        $assertionLength = strlen($this->assertNext);
        if ((strlen($string) + $i) < $assertionLength) {
            if ($string[$i] === $this->assertNext[0]) {
                for ($x = 1; $x < $assertionLength; $x++) {
                    if ($string[$i + $x] !== $this->assertNext[$i + $x]) {
                        return ['j' => $i, 'args' => []];
                    }
                }


            }
        }
    }
}
