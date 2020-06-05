
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
### `ConcParser`

This encapsulates the "concatenation" parser combinator by concatenating a finite sequence of internal parsers. If the sequence is 
empty, this is equivalent to `Ferno\Loco\EmptyParser`, above.

Callback is passed one argument for every internal parser, each argument containing the result from that parser. For example, 
`new Ferno\Loco\ConcParser(array($a, $b, $c), $callback)` will pass three arguments to its callback. The first contains the result from 
parser `$a`, the second the result from parser `$b` and the third the result from parser `$c`. The default callback returns the 
arguments in the form of an array: `return func_get_args();`.

    new Ferno\Loco\ConcParser(
      array(
        new Ferno\Loco\RegexParser("/^<([a-zA-Z_][a-zA-Z_0-9]*)>/", function($match0, $match1) { return $match1; }),
        new Ferno\Loco\StringParser(", "),
        new Ferno\Loco\RegexParser("/^<(\d\d\d\d-\d\d-\d\d)>/",     function($match0, $match1) { return $match1; }),
        new Ferno\Loco\StringParser(", "),
        new Ferno\Loco\RegexParser("/^<([A-Z]{2}[0-9]{7})>/",       function($match0, $match1) { return $match1; }),
      ),
      function($name, $comma1, $opendate, $comma2, $ref) { return new Account($accountname, $opendate, $ref); }
    );
    // match something like "<Williams>, <2011-06-30>, <GH7784939>"
    // return new Account("Williams", "2011-06-30", "GH7784939")



-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
