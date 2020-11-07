<?php declare(strict_types=1);

namespace Ab\LocoX\Clause\Nonterminal;

use Ab\LocoX\Grammar;
use Ab\LocoX\Exception\{GrammarException, ParseFailureException};
use Ab\LocoX\Clause\TerminalClause;

/**
 * UntilString matches everything up until any one of the provided
 * $lookaheadStrings is encountered. So this works like a negative
 * lookahead regular expression, but is less flexible.
 */
class UntilString extends TerminalClause
{
    private $lookaheadStrings;

    public function __construct($lookaheadStrings, $callback = null)
    {
        if (! is_array($lookaheadStrings)) {
            throw new GrammarException('$lookaheadStrings must be an array');
        }

        if (0 === count($lookaheadStrings)) {
            throw new GrammarException('$lookaheadStrings must not be empty');
        }
        $this->lookaheadStrings = $lookaheadStrings;

        $this->string = 'new ' . __CLASS__ . '(' . Grammar::serializeGrammar($lookaheadStrings) . ')';

        parent::__construct($callback);
    }

    /**
     * default callback: return the string that was matched
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    public function getResult($string, $i = 0)
    {
        $lookaheadFirstStringPos = strlen($string);
        foreach ($this->lookaheadStrings as $lookahead) {
            $pos = strpos($string, $lookahead, $i);
            if (false !== $pos && $pos < $lookaheadFirstStringPos) {
                $lookaheadFirstStringPos = $pos;
            }
        }

        if ($lookaheadFirstStringPos === $i) {
            throw new ParseFailureException($this . ' did not match anything ', $i, $string);
        }

        return [
            'j' => $lookaheadFirstStringPos,
            'args' => [substr($string, $i, $lookaheadFirstStringPos - $i)],
        ];
    }

    public function evaluateNullability(): bool
    {
        return false;
    }
}
