
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----

### `EmptyParser`

Finds the empty string (and always succeeds). Callback is passed no arguments. Default callback returns `null`.

    new Ferno\Loco\EmptyParser();
    // returns null

    new Ferno\Loco\EmptyParser(
      function() { return array(); }
    );
    // return an empty array instead



-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
