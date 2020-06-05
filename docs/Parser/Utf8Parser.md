
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
### `Utf8Parser`

Matches a single UTF-8 character. You can optionally supply a blacklist of characters which will *not* be matched.

    new Ferno\Loco\Utf8Parser(array("<", ">", "&"));
    // any UTF-8 character except the three listed

Callback is passed one argument, the string that was matched. The default callback returns the first argument i.e. the string.

For best results, alternate (see `Ferno\Loco\LazyAltParser` below) with `Ferno\Loco\StringParsers` for e.g. `"&lt;"`, `"&gt;"`, 
`"&amp;"` and other HTML character entities.




-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
