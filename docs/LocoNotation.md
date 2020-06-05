
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

In this version of the code, LocoNotation is available as both a Grammar Factory and as an example.  The following documentation only refers to it as an example.

-----
### [examples/locoNotation.php](https://github.com/ferno/loco/blob/master/examples/locoNotation.php)

Defines `$locoGrammar`, which parses a grammar presented in "Loco notation" and returns a `Ab\LocoX\Grammar` object capable of parsing that grammar.

"Loco notation" (for lack of a better name) is an extension of Backus-Naur Form which gives access to all the `Ab\LocoX\MonoParser`s that Loco makes available. The following parsers are already effectively available in most grammar notations:

* `Ab\LocoX\EmptyParser` - Just have an empty string or an empty right-hand side to a rule. Some notations also permit an explicit "epsilon" symbol.
* `Ab\LocoX\StringParser` - Invariably requires a simple string literal in single or double quotes.
* `Ab\LocoX\ConcParser` - Usually you put multiple tokens in a row and they will be matched consecutively. In EBNF, commas must be used as separators.
* `Ab\LocoX\LazyAltParser` - Alternation is achieved using a pipe, `|`, between possibilities.
* `Ab\LocoX\GreedyMultiParser` - Most notations provide some ability to make a match optional (typically square brackets), and/or to match an unlimited number of times (typically an asterisk or braces).

I had to invent new notation for the following:

* `Ab\LocoX\RegexParser` - Put your regex between slashes, just like in Perl.
* `Ab\LocoX\Utf8Parser` - To match any single UTF-8 character, put a full stop, `.`. To blacklist some characters, put the blacklisted characters between `[^` and `]`.

In both cases I borrowed notation from the standard regular expression syntax, because why not stay with the familiar?

In all cases where a "literal" is provided (strings, regexes, UTF-8 exceptions), you can put the corresponding closing delimiter (i.e. `"`, `'`, `/` or `]`) inside the "literal" by escaping it with a backslash. E.g.: `"\""`, `'\''`, `/\//`, `[^\]]`. You can also put a backslash itself, if you escape it with a second backslash. E.g.: `"\\"`, `'\\'`, `/\\/`, `[^\\]`.

#### Sample grammar in Loco notation

Remember [examples/simpleComment.php](https://github.com/ferno/loco/blob/master/examples/simpleComment.php)? Here is that grammar in Loco notation.

    comment    ::= whitespace block*
    block      ::= h5 whitespace | p whitespace
    p          ::= '<p'      whitespace '>' text '</p'      whitespace '>'
    h5         ::= '<h5'     whitespace '>' text '</h5'     whitespace '>'
    strong     ::= '<strong' whitespace '>' text '</strong' whitespace '>'
    em         ::= '<em'     whitespace '>' text '</em'     whitespace '>'
    br         ::= '<br'     whitespace '/>'
    text       ::= atom*
    atom       ::= [^<>&] | '&' entity ';' | strong | em | br
    entity     ::= 'gt' | 'lt' | 'amp'
    whitespace ::= /[ \n\r\t]*/

See how I've put `/[ \n\r\t]*/` to match an unlimited sequence of whitespace. This could be achieved using more rules and StringParsers, but RegexParsers are more powerful and more elegant.

Also see how I've put `[^<>&]` to match "any UTF-8 character except a `<`, a `>` or a `&`".

#### String in the sample grammar

    <h5>  Title<br /><em\n><strong\n></strong>&amp;</em></h5>
       \r\n\t 
    <p  >&lt;</p  >



-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
