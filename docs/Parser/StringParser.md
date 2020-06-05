
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
### `StringParser`

Finds a static string. Callback is passed one argument, the string that was matched. Yes, that's effectively the same function 
call each time. Default callback returns the first argument i.e. the string.

    new Ferno\Loco\StringParser("name");
    // returns "name"

    new Ferno\Loco\StringParser(
      "name",
      function($string) { return strrev($string); }
    );
    // returns "eman"






-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
