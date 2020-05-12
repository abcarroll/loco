<?php

namespace Ab\LocoX;

use Ab\LocoX\Exception\GrammarException;
use Ab\LocoX\Exception\ParseFailureException;
use Exception;
use function call_user_func_array;
use function is_array;
use function is_callable;
use function is_string;
use function strlen;
use function var_export;

abstract class MonoParser implements ParserInterface
{
    // A string form for any parser should be generated at instantiation time.
    // This string should be *approximately* the "new MonoParser()" syntax,
    // although stringifying the callback is problematic so don't bother trying.
    // serialiseArray() helps with array arguments (var_export is no good because
    // it leaves line breaks!)
    public $internals;

    public $callback;

    // An array of internal parsers, which are called recursively by and hence
    // "exist inside of" this parser. These may be actual MonoParser
    // objects.
    // They may also be references to (i.e. string names of) other parsers
    // elsewhere within the Grammar object within which $this presumably exists.
    // The Grammar object will resolve() these strings into references
    // to the real parsers at Grammar instantiation time.
    // This list is empty for "static" parsers
    public $nullable = false;

    // A function to apply to the result of whatever this parser just parsed.
    // The arguments supplied to this callback depend on the parser class;
    // check!
    protected $string;

    public function __construct($internals, $callback)
    {
        if (!is_string($this->string)) {
            throw new Exception('You need to populate $string');
        }
        // Perform basic validation.
        if (!is_array($internals)) {
            throw new GrammarException(var_export($internals, true) . ' should be an array');
        }
        foreach ($internals as $internal) {
            if (!is_string($internal) && !$internal instanceof MonoParser) {
                throw new GrammarException(var_export(
                        $internal,
                        true
                    ) . ' should be either a string or a MonoParser');
            }
        }
        $this->internals = $internals;
        // if null, set default callback
        if (null === $callback) {
            $callback = [$this, 'defaultCallback'];
        }
        if (!is_callable($callback)) {
            throw new GrammarException('Callback should be a callable function');
        }
        $this->callback = $callback;
    }

    public function __toString()
    {
        return $this->string;
    }

    abstract public function defaultCallback();

    /**
     * apply callback to returned value before returning it
     *
     * @param mixed $string
     *
     * @param int $currentPosition
     * @return array
     * @psalm-return array{j: mixed, value: mixed}
     */
    public function match(string $string, int $currentPosition = 0): array
    {
        $result = $this->getResult($string, $currentPosition);

        return ['j' => $result['j'], 'value' => call_user_func_array($this->callback, $result['args'])];
    }

    /**
     * try to match this parser at the specified point.
     * returns j and args to pass to the callback, or throws exception on failure
     *
     * @param mixed $string
     * @param int $currentPosition
     */
    abstract public function getResult(string $string, int $currentPosition = 0);

    // Every parser assumes that it is non-nullable from the outset

    public function parse($string)
    {
        $result = $this->getResult($string, 0);
        if ($result['j'] !== strlen($string)) {
            throw new ParseFailureException('Parsing completed prematurely', $result['j'], $string);
        }
        // notice how this isn't called until AFTER we've verified that
        // the whole thing has been parsed
        return call_user_func_array($this->callback, $result['args']);
    }

    /**
     * Evaluate the nullability of this parser with respect to each of its
     * internals. This function must NOT simply "return $nullable;", whose content
     * may be out of date; this function must NOT modify $nullable, either, because
     * that is not for this function to do; this function must NOT recursively
     * call evaluateNullability() on any of its internals because that could easily
     * result in a stack overflow.
     * Just gets $nullable for each internal, if any.
     * This has to be called after all strings have been resolved to parser references.
     */
    abstract public function evaluateNullability(): bool;

    /**
     * The immediate first-set of a parser is the set of all internal parsers
     * which could be matched first. For example, if A = B . C then the first-set
     * of A is usually {B}. If B is nullable, then C could also be matched first, so the
     * first-set is {B, C}.
     * This has to be called after the "nullability flood fill" is complete,
     * or "Called method of non-object" exceptions will arise
     *
     * @psalm-return list<MonoParser>
     *
     * @return ParserInterface[]
     */
    abstract public function firstSet(): array;
}
