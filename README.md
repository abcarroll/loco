# A.B.'s LocoX Parsing Framework for PHP

LocoX is a LL-regular 'monadic' recursive descent parser which has the ability to parse several BNF-like formal grammar notations as well as derive grammar from plain PHP code as objects.  

Using LocoX, you will be able to generate parsers/parser-lexers for:

- Declarative File Formats such as JSON (included!), CSV, and all of the slight variations with included error recovery.
- New API protocols: JSON+HAL? Easy – add comments and error recovery while you're at it, because why not?  GraphQL?  I'd bet it can parse it faster than the Leading Brand.
- Network Protocols such as IRC, IMAP, FTP, and so on.  And not just line-based protocols, complex binary protocols such as HTTP/2, ZeroMQ, and other "tightly-packed" binfmts would be prime candidates to parse using LocoX.
- Formal grammars! … Aside from the above ideas, LocoX has built-in BNF, EBNF, Wirth, and other formal grammar parsers which generate parsers from the given grammar.
- Yes, you can parse HTML, XHTML, XML, SGML, and all of those formats that you should not parse with a regular expression with LocoX.  You can even **convert regular expression** to LocoX parsers.
- Configuration file formats, formal grammar notations, and virtually anything Yacc,  Bison, Flex, re2c, and other parser generators can parse without the necessary complexity of LR/LALR table-driven parsers.

## Philosphy of Operation

LocoX works via a very simple and battle-proven basis / philosophy: 

- Many small things 
- do one thing and do it well
- compose together to form a larger system

You may recognize this as the "un\*x philosphy" which describes how Un\*x shells (today, Linux/BSD really) are incredibly
powerful and robust due to the fact you may string many small tools together via `|` pipes.  This idea, extended out to system
and network daemons form the most robust/stable operating systems available today: OpenBSD, Linux, and others.

LocoX uses the same idea, but using components of parsing.  In LocoX, everything is "a matcher".  This includes what would be
quantifiers (`*`, `+`, `{1,10}` – telling _quantity to match_) and logical gates (`|` for 'OR' or alternation; AND is implicit in regex). 

Creating a complex grammar in LocoX is very similar to functional programming.  The best part about LocoX is it can be used for 
both **_very simple_** parsing: JSON, CSV, etc, all the way up to **full-blown programming languages** and **complex grammars **
**with hundreds of symbols.**

Since we parse a lot of things, LocoX comes with a huge ready-made library of common parser-pieces and stanzas within its
Builder API.  Automatically generate parser components for common parts such as quoted strings, parameter lists, binary and
mathematical expressions, and so on.  Just take a look at the [Builder API](#).

## Getting Started

…………

Docs…