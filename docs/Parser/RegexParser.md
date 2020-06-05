
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
### `RegexParser`

Matches a regular expression. The regular expression must be anchored at the beginning of the substring supplied to match, using 
`^`. Otherwise, there's no way to stop PHP from matching elsewhere entirely in the expression, which is very bad. Caution: 
formations like `/^a|b/` only anchor the `"a"` at the start of the string; a `"b"` might be matched anywhere! You should use 
`/^(a|b)/` or `/^a|^b/`.

Callback is passed one argument for each sub-match. For example, if the regex is `/^ab(cd(ef)gh)ij/` then the first argument is 
the whole match, `"abcdefghij"`, the second argument is `"cdefgh"` and the third argument is `"ef"`. The default callback returns 
only the first argument, the whole match.

    new Ferno\Loco\RegexParser("/^'([a-zA-Z_][a-zA-Z_0-9]*)'/");
    // returns the full match including the single quotes
  
    new Ferno\Loco\RegexParser(
      "/^'([a-zA-Z_][a-zA-Z_0-9]*)'/",
      function($match0, $match1) { return $match1; }
    );
    // discard the single quotes and returns only the inner string




-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
