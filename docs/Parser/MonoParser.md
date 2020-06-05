
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----

### `MonoParser`

Abstract base class from which all parsers inherit. Can't be instantiated. "Mono" means the parser returns one result, or fails.

`Ab\LocoX\MonoParser` has one important method, `match($string, $i = 0)`, which either returns the successful match in the form 
of an `array("j" => 9, "value" => "something")`, or throws a `Ab\LocoX\ParseFailureException`.

There is also the more useful method `parse($string)`, which either returns the parsed value `"something"` or throws a 
`Ab\LocoX\ParseFailureException` if the match fails or doesn't occupy the entire length of the supplied string.




-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
