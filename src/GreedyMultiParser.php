<?php

namespace Ab\LocoX;

use Ab\LocoX\Exception\GrammarException;
use Ab\LocoX\Exception\ParseFailureException;
use function func_get_args;
use function var_export;

/**
 * Callback accepts a single argument containing all submatches, however many
 */
class GreedyMultiParser extends MonoParser
{
    public $optional;

    private $lower;

    public function __construct($internal, $lower, $upper, $callback = null)
    {
        $this->lower = $lower;
        if (null === $upper) {
            $this->optional = null;
        } else {
            if ($upper < $lower) {
                throw new GrammarException("Can't create a " . __CLASS__ . ' with lower limit ' . var_export(
                        $lower,
                        true
                    ) . ' and upper limit ' . var_export($upper, true));
            }
            $this->optional = $upper - $lower;
        }
        $this->string = 'new ' . __CLASS__ . '(' . $internal . ', ' . var_export(
                $lower,
                true
            ) . ', ' . var_export($upper, true) . ')';
        parent::__construct([$internal], $callback);
    }

    /**
     * default callback: just return the list
     *
     * @psalm-return list<mixed>
     */
    public function defaultCallback(): array
    {
        return func_get_args();
    }

    /**
     * @return array (array|mixed)[]
     *
     * @psalm-return array{j: mixed, args: list<mixed>}
     */
    public function getResult(string $string, int $currentPosition = 0): array
    {
        $result = ['j' => $currentPosition, 'args' => []];
        // First do the non-optional segment
        // Any parse failures here are terminal
        for ($k = 0; $k < $this->lower; $k++) {
            $match = $this->internals[0]->match($string, $result['j']);
            $result['j'] = $match['j'];
            $result['args'][] = $match['value'];
        }
        // next, the optional segment
        // null => no upper limit
        for ($k = 0; null === $this->optional || $k < $this->optional; $k++) {
            try {
                $match = $this->internals[0]->match($string, $result['j']);
                $result['j'] = $match['j'];
                $result['args'][] = $match['value'];
            } catch (ParseFailureException $e) {
                break;
            }
        }

        return $result;
    }

    /**
     * nullable if lower limit is zero OR internal is nullable.
     */
    public function evaluateNullability(): bool
    {
        return 0 === $this->lower || true === $this->internals[0]->nullable;
    }

    /**
     * This parser contains only one internal
     *
     * @psalm-return array{0: mixed}
     */
    public function firstSet(): array
    {
        return [$this->internals[0]];
    }
}
