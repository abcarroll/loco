<?php

class Trunk
{
    private int $trueLength = 0;

    public function hasChildren()
    {
        return $this->trueLength > 0;
    }

    public function children()
    {

    }

    public function renderAsText()
    {
        $this->apply(function())
    }
}
