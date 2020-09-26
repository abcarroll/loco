<?php

namespace Ab\LocoX;

use Ab\LocoX\Exception\ParseFailureException;
use Exception;

/**
 * These parsers are all unusual in that instead of returning a complete
 * set of js and tokens, each returns either a single successful combination of
 * j and result, or throws a ParseFailureException. These are, then, "monoparsers"
 *
 * @link http://en.wikipedia.org/wiki/Parser_combinator
 */
abstract class MonoParser extends TopDownParser
{
    /**
     * A string form for any parser should be generated at instantiation time.
     * This string should be *approximately* the "new MonoParser()" syntax,
     * although stringifying the callback is problematic so don't bother trying.
     * serialiseArray() helps with array arguments (var_export is no good because
     * it leaves line breaks!)
     */
    protected $string;

    public function __toString()
    {
        return $this->string;
    }

    /**
     * An array of internal parsers, which are called recursively by and hence
     * "exist inside of" this parser. These may be actual MonoParser
     * objects.
     * They may also be references to (i.e. string names of) other parsers
     * elsewhere within the Grammar object within which $this presumably exists.
     * The Grammar object will resolve() these strings into references
     * to the real parsers at Grammar instantiation time.
     * This list is empty for "static" parsers
     *
     * @var Parser[]
     */
    public $internals;

    /**
     * A function to apply to the result of whatever this parser just parsed.
     * The arguments supplied to this callback depend on the parser class;
     * check!
     */
    public $callback;

    abstract public function defaultCallback();

    /**
     * try to match this parser at the specified point.
     * returns j and args to pass to the callback, or throws exception on failure
     *
     * @param mixed $string
     * @param mixed $i
     */
    abstract public function getResult($string, $i = 0);

    public function __construct($internals, $callback)
    {
        if (! is_string($this->string)) {
            throw new Exception('You need to populate $string');
        }

        // Perform basic validation.
        if (! is_array($internals)) {
            throw new GrammarException(var_export($internals, true) . ' should be an array');
        }
        foreach ($internals as $internal) {
            if (! is_string($internal) && ! ($internal instanceof MonoParser)) {
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
        if (! is_callable($callback)) {
            throw new GrammarException('Callback should be a callable function');
        }
        $this->callback = $callback;
    }

    /**
     * apply callback to returned value before returning it
     *
     * @param mixed $string
     * @param mixed $i
     */
    public function match($string, $i = 0)
    {
        $result = $this->getResult($string, $i);

        return [
            'j' => $result['j'],
            'value' => call_user_func_array($this->callback, $result['args'])
        ];
    }

    /**
     * Parse: try to match this parser at the beginning of the string
     * Return the result only on success, or throw exception on failure
     * or if the match doesn't encompass the whole string
     *
     * @param mixed $string
     */
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
}
