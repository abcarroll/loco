<?php

namespace Ab\LocoX;

class ClauseChildForwarder extends Clause
{
    private array $calls = [];
    /**
     * @var ClauseChildForwarder
     */
    private ClauseChildForwarder $parent;

    public function __construct(ClauseChildForwarder $parent = null)
    {
        $this->parent = $parent;
    }

    private function registerCall(string $name, array $args = [])
    {
        $this->calls[] = $name;

        return new self();
    }

    public function firstSet()
    {
        $this->registerCall(__FUNCTION__);

        return new self();
    }

    public function evaluateNullability()
    {
        $this->registerCall(__FUNCTION__);

        return new self();
    }

    public function match($string, $i = 0)
    {
        $this->registerCall(__FUNCTION__, [$string, $i]);

        return new self();
    }

    public function __call($name, $args)
    {
        $this->registerCall($name, $args);

        return new self();
    }

    public function getCalls()
    {

    }
}


$x = new ClauseChildForwarder();
$x->children()->firstSet()->firstSet();

$x->getCalls();
