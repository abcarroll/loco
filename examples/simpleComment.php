<?php
namespace Ab\LocoX;

use Ab\LocoX\Clause\Nonterminal\GreedyStarParser;
use Ab\LocoX\Clause\Nonterminal\OrderedChoice;
use Ab\LocoX\Clause\Nonterminal\Sequence;
use Ab\LocoX\Clause\Terminal\RegexParser;
use Ab\LocoX\Clause\Terminal\StringParser;
use Ab\LocoX\Clause\Terminal\Utf8Parser;
use Exception;

require_once __DIR__ . '/../vendor/autoload.php';

# This code is in the public domain.
# http://qntm.org/loco

$simpleCommentGrammar = new Grammar(
	"<comment>",
	array(
		"<comment>" => new GreedyStarParser(
			"<blockorwhitespace>",
			function() { return implode("", func_get_args()); }
		),
		"<blockorwhitespace>" => new OrderedChoice(
			array("<h5>", "<p>", "WHITESPACE")
		),
		"<p>" => new Sequence(
			array("OPEN_P", "<text>", "CLOSE_P"),
			function($open_p, $text, $close_p) { return $open_p.$text.$close_p; }
		),
		"<h5>" => new Sequence(
			array("OPEN_H5", "<text>", "CLOSE_H5"),
			function($open_h5, $text, $close_h5) { return $open_h5.$text.$close_h5; }
		),
		"<strong>" => new Sequence(
			array("OPEN_STRONG", "<text>", "CLOSE_STRONG"),
			function($open_strong, $text, $close_strong) { return $open_strong.$text.$close_strong; }
		),
		"<em>" => new Sequence(
			array("OPEN_EM", "<text>", "CLOSE_EM"),
			function($open_em, $text, $close_em) { return $open_em.$text.$close_em; }
		),
		"<text>" => new GreedyStarParser(
			"<atom>",
			function() { return implode("", func_get_args()); }
		),
		"<atom>" => new OrderedChoice(
			array("<char>", "<strong>", "<em>", "FULL_BR")
		),
		"<char>" => new OrderedChoice(
			array("UTF8_EXCEPT", "GREATER_THAN", "LESS_THAN", "AMPERSAND")
		),

		# actual lexables here

		"WHITESPACE"   => new RegexParser("#^[ \n\r\t]+#"),
		"OPEN_P"       => new RegexParser("#^<p[ \n\r\t]*>#"),
		"CLOSE_P"      => new RegexParser("#^</p[ \n\r\t]*>#"),
		"OPEN_H5"      => new RegexParser("#^<h5[ \n\r\t]*>#"),
		"CLOSE_H5"     => new RegexParser("#^</h5[ \n\r\t]*>#"),
		"OPEN_EM"      => new RegexParser("#^<em[ \n\r\t]*>#"),
		"CLOSE_EM"     => new RegexParser("#^</em[ \n\r\t]*>#"),
		"OPEN_STRONG"  => new RegexParser("#^<strong[ \n\r\t]*>#"),
		"CLOSE_STRONG" => new RegexParser("#^</strong[ \n\r\t]*>#"),
		"FULL_BR"      => new RegexParser("#^<br[ \n\r\t]*/>#"),

		"UTF8_EXCEPT"  => new   Utf8Parser(array("<", ">", "&")), // any UTF-8 character except <, > or &
		"GREATER_THAN" => new StringParser("&gt;"),               // ... or an escaped >
		"LESS_THAN"    => new StringParser("&lt;"),               // ... or an escaped <
		"AMPERSAND"    => new StringParser("&amp;"),              // ... or an escaped &
	)
);

// if executing this file directly, run unit tests
if(__FILE__ !== $_SERVER["SCRIPT_FILENAME"]) {
	return;
}

$start = microtime(true);
$string = $simpleCommentGrammar->parse("<h5>  Title<br /><em\n><strong\n></strong>&amp;</em></h5>   \r\n\t <p  >&lt;</p  >");
print("Parsing completed in ".(microtime(true)-$start)." seconds\n");
var_dump($string === "<h5>  Title<br /><em\n><strong\n></strong>&amp;</em></h5>   \r\n\t <p  >&lt;</p  >");

foreach(
	array(
		"<h5 style=\"\">", // rogue "style" attribute
		"&",               // unescaped AMPERSAND
		"<",               // unescaped LESS_THAN
		"salkhsfg>",       // unescaped GREATER_THAN
		"</p",             // incomplete CLOSE_P
		"<br"              // incomplete FULL_BR
	) as $string
) {
	try {
		$simpleCommentGrammar->parse($string);
		var_dump(false);
	} catch(Exception $e) {
		var_dump(true);
	}
}
