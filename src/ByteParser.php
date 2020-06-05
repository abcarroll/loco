<?php

namespace Ferno\Loco;

/**
 * ByteParser consumes all bytes until a particular byte sequence is reached, or fail if that sequence never occurs.
 *
 * For example, a ByteParser(["a", "b"]) on input "zzzz\x00a", assuming the \x00 is interpreted as NULL byte, will
 * return back to the caller "zzzz\x00", offset +5, success -- next byte on chunk "a".
 *
 * If you are matching text, you may wish to look at the Utf8Parser which exhibits the precise same behaviour, however
 * is UTF-8 aware as the name implies.  This ByteParser is significantly more performant, of course, not needing to
 * validate the UTF-8.
 *
 * @author A.B. Carroll <ben@hl9.net>
 */
class ByteParser extends MonoParser
{
    private $matchExceptList = [];

    public function __construct($stopOnCharactersBytes, $callback = null)
    {
        $this->matchExceptList = array_combine($stopOnCharactersBytes, $stopOnCharactersBytes);
        parent::__construct([] ,$callback);
    }

    /**
     * @return array
     *
     * @psalm-return array{pos: mixed, args: mixed}
     */
    public function getResult($string, $offset = 0): array
    {
        $inputMaxLen = strlen($string);

        for ($x = $offset; $x < $inputMaxLen && !isset($this->matchExceptList[$string[$x]]); $x++);

        if ($x === $offset) {
            throw new ParseFailureException(
                $this . " could not find string terminator: " . var_export($this->matchExceptList, true), $offset, $string
            );
        }

        return [
            "pos"  => $x,
            "args" => $string
        ];
    }

    /**
     * default callback: just return the string that was matched
     * @return array
     */
    public function defaultCallback(): array
    {
        return func_get_arg(0);
    }

    /**
     * @return false
     */
    public function evaluateNullability(): bool
    {
        return false;
    }

    public function firstSet()
    {
        return null;
    }
}
