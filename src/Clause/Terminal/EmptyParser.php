<?php declare(strict_types=1);

namespace Ab\LocoX\Clause\Terminal;

use Ab\LocoX\Clause\TerminalClause;

/**
 * Match the empty string
 */
class EmptyParser extends TerminalClause
{
    public function __construct($callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '()';
        parent::__construct($callback);
    }

    /**
     * default callback returns null
     */
    public function defaultCallback()
    {
        return null;
    }

    /**
     * Always match successfully, pass no args to callback
     *
     * @param mixed $string
     * @param mixed $i
     */
    public function getResult($string, $i = 0)
    {
        return [
            'j' => $i,
            'args' => []
        ];
    }

    /**
     * emptyparser is nullable.
     */
    public function evaluateNullability(): bool
    {
        return true;
    }
}
