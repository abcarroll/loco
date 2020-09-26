<?php

abstract class Node implements \Countable
{
    private int $length = 0;

    public function hasChildren()
    {
        return $this->length > 0;
    }

    public function children()
    {
        if($this->length === 0) {
            return [];
        }

        return $this->getPublicChildren();
    }

    public function apply(callable $fn)
    {
        foreach($this->children() as $child)
        {
            if($child instanceof Node) {
                $child->apply($fn);
            }
        }
    }

    public function count()
    {
        return $this->length;
    }

    abstract protected function internalChildren();
    abstract protected function internalApply();
}
