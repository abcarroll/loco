
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
# Loco

Loco is a parsing library for PHP.

Loco uses single-valued parsers called `MonoParser`s. A conventional, "enthusiastic" parser returns a set of possible results, 
which is empty if parsing is not possible. A "lazy" parser returns one possible result on the first call, and then returns further 
results with each subsequent call until no more are possible. In contrast, `MonoParser`s simply return a single result or failure. 
This in turn **makes backtracking impossible**, which has two effects:

* it reduces expressive power to only certain **unambiguous** context-free grammars
* it prevents parsing time from becoming exponential.

Loco directly to parses strings, requiring no intermediate lexing step.

Loco detects infinite loops (e.g. `(|a)*`) and [left recursion](http://en.wikipedia.org/wiki/Left_recursion) (e.g. `A -> Aa`) at 
grammar creation time.

-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
