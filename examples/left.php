<?php
namespace Ab\LocoX;

use Ab\LocoX\Clause\Nonterminal\GreedyStarParser;
use Ab\LocoX\Clause\Nonterminal\LazyAltParser;
use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\StringParser;
use Ab\LocoX\Exception\GrammarException;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Left-recursion in Loco, demonstration.
 *
 * Left-recursive grammars cannot be parsed using a recursive descent approach.
 * Loco detects left-recursion in a new grammar and raises an exception.
 * How do we get around this?
 */

/**
 * minus($minuend, $subtrahend) is a left-associative operator.
 * e.g. "5 - 4 - 3" means "(5 - 4) - 3 = -2", not "5 - (4 - 3) = 4".
 */
function minus($minuend, $subtrahend) {
	return $minuend - $subtrahend;
}

# N -> number
$N = new RegexParser(
	"#^(0|[1-9][0-9]*)#",
	function($match) { return (int) $match; }
);

# P -> "-" N
$P = new Sequence(
	array(new StringParser("-"), $N),
	function($minus, $n) { return $n; }
);

# Naive left-recursive grammar looks like this and raises an exception
# when instantiated.
try {
	# S -> N
	# S -> S P
	$grammar = new Grammar(
		"S",
		array(
			"S" => new LazyAltParser(
				array(
					"N",
					new Sequence(
						array("S", "P"),
						"minus"
					)
				)
			),
			"P" => $P,
			"N" => $N
		)
	);
    assert(false);
} catch (GrammarException $e) {
	# Left-recursive in S
    assert(true);
}

# Fix the grammar like so:
# S -> N P*
$grammar = new Grammar(
	"S",
	array(
		"S" => new Sequence(
			array(
				$N,
				new GreedyStarParser("P")
			),
			function($n, $ps) {
				return array_reduce($ps, "minus", $n); # clever bit
			}
		),
		"P" => $P,
		"N" => $N
	)
);

assert($grammar->parse("5-4-3") === -2); # true
