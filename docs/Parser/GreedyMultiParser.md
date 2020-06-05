
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
### `GreedyMultiParser`

This encapsulates the "Kleene star closure" parser combinator to match single internal parser multiple (finitely or infinitely 
many) times. With a finite upper bound, this is more or less equivalent to `Ferno\Loco\ConcParser`, above. With an infinite upper bound, this 
gets more interesting. `Ferno\Loco\GreedyMultiParser`, as the name hints, will match as many times as it can before returning. 
There is no option for returning multiple matches simultaneously; only the largest match is returned. And there is no option for 
backtracking and trying to consume more or fewer instances.

Callback is passed one argument for every match. For example, `new Ferno\Loco\GreedyMultiParser($a, 2, 4, $callback)` could pass 
2, 3 or 4 arguments to its callback. `new GreedyMultiParser($a, 0, null, $callback)` has an unlimited upper bound and could pass 
an unlimited number of arguments to its callback. (PHP seems to have no problem with this.) The default callback returns all of 
the arguments in the form of an array: `return func_get_args();`.

Remember that a PHP function can be defined as `function(){...}` and still accept an arbitrary number of arguments.

    new Ferno\Loco\GreedyMultiParser(
      new Ferno\Loco\LazyAltParser(
        array(
          new Ferno\Loco\Utf8Parser(array("<", ">", "&")),                         // match any UTF-8 character except <, > or &
          new Ferno\Loco\StringParser("&lt;",  function($string) { return "<"; }), // ...or an escaped < (unescape it)
          new Ferno\Loco\StringParser("&gt;",  function($string) { return ">"; }), // ...or an escaped > (unescape it)
          new Ferno\Loco\StringParser("&amp;", function($string) { return "&"; })  // ...or an escaped & (unescape it)
        )
      ),
      0,                                                  // at least 0 times
      null,                                               // at most infinitely many times
      function() { return implode("", func_get_args()); } // concatenate all of the matched characters together
    );
    // matches a continuous string of valid, UTF-8 encoded HTML text
    // returns the unescaped string



-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
