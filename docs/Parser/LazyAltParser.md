
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
### `LazyAltParser`

This encapsulates the "alternation" parser combinator by alternating between several internal parsers. The key word here is 
"lazy". As soon as one of them matches, that result is returned, and that's the end of the story. There is no capability to merge 
the results from several of the internal parsers, and there is no capability for returning (backtracking) to this parser and 
trying to retrieve other results if the first one turns out to be bogus.

Callback is passed one argument, the sole successful internal match. The default callback returns the first argument directly.

    new Ferno\Loco\LazyAltParser(
      array(
        new Ferno\Loco\StringParser("foo"),
        new Ferno\Loco\StringParser("bar")
      )
    );
    // returns either "foo" or "bar"





-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
