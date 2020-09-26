# Common Parser Notation

This document describes a vendor-neutral and language-agnostic parser notation.

This parser notation is slightly different than a format such as PEG in that PEG is not _precisely_ formal and also 
only lends itself to recursive-descent/top-down parsing.

CPN attempts to lend itself to multiple styles of parsing such as table-driven CLR/LR/LALR, CYK, GLR, specialized
parsers such as Pikaparsers, and of course typical recursive-descent style parsers.

CPN can be written in JSON, YAML, or XML.  It doesn't lend itself well to being written by humans.  Instead, CPN comes
with tools to convert many other grammar notations to CPN.

```yaml
Parser:
  engine: recursive-descent
  recursive-descent:
    some-name:
      match: sequence
      
``` 
