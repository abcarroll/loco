<?php declare(strict_types=1);

namespace Ab\LocoX\Clause\Terminal;

use Ab\LocoX\Exception\{GrammarException, ParseFailureException};
use Ab\LocoX\Clause\TerminalClause;

/**
 * Parser uses a regex to match itself. Regexes are time-consuming to execute,
 * so use StringParser to match static strings where possible.
 * Regexes can match multiple times in theory, but this pattern returns a singleton
 * Callback should accept an array of all the matches made
 */
class RegexParser extends TerminalClause
{
    private $pattern;

    public function __construct($pattern, $callback = null)
    {
        $this->string = 'new ' . __CLASS__ . '(' . var_export($pattern, true) . ')';
        if ('^' !== substr($pattern, 1, 1)) {
            throw new GrammarException($this . " doesn't anchor at the beginning of the string!");
        }
        $this->pattern = $pattern;
        parent::__construct($callback);
    }

    /**
     * default callback: return only the main match
     */
    public function defaultCallback()
    {
        return func_get_arg(0);
    }

    public function getResult($string, $i = 0)
    {
        if (1 === preg_match($this->pattern, substr($string, $i), $matches)) {
            return [
                'j' => $i + strlen($matches[0]),
                'args' => $matches
            ];
        }

        throw new ParseFailureException($this . ' could not match expression ' . var_export(
            $this->pattern,
            true
        ), $i, $string);
    }

    /**
     * nullable only if regex matches ""
     */
    public function evaluateNullability(): bool
    {
        return 1 === preg_match($this->pattern, '', $matches);
    }
}
