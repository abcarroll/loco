
NOTE: This is documentation from the original Loco project, and may be slightly out of date or use PHP5.x-esqe (but still quite valid) syntax.  Pull requests welcome!

-----
### `Grammar`

All of the above is well and good, but it doesn't complete the picture. Firstly, it makes our parsers quite large and confusing to 
read when they nest too much. Secondly, it makes recursion very difficult; a parser cannot easily be placed inside itself, for 
example. Without recursion, all we can parse is regular languages, not context-free languages.

The `Ferno\Loco\Grammar` class makes this very easy. At its heart, `Ferno\Loco\Grammar` is just another `Ferno\Loco\MonoParser`. 
But `Ferno\Loco\Grammar` accepts an associative array of parsers as input -- meaning each one comes attached to a name. The 
parsers inside it, meanwhile, can refer to other parsers by name instead of containing them directly. `Ferno\Loco\Grammar` 
resolves these references at instantiation time, as well as detecting anomalies like left recursion, names which refer to parsers 
which don't exist, dangerous formations such as new `Ferno\Loco\GreedyMultiParser(new Ferno\Loco\EmptyParser(), 0, null)`, and so 
on.

Here's a simple `Ferno\Loco\Grammar` which can recognise (some) valid HTML paragraphs and return the text content of those paragraphs:

    $p = new Ferno\Loco\Grammar(
      "paragraph",
      array(
        "paragraph" => new Ferno\Loco\ConcParser(
          array(
            "OPEN_P",
            "CONTENT",
            "CLOSE_P"
          ),
          function($open_p, $content, $close_p) {
            return $content;
          }
        ),

        "OPEN_P" => new Ferno\Loco\StringParser("<p>"),

        "CONTENT" => new Ferno\Loco\GreedyMultiParser(
          "UTF-8 CHAR",
          0,
          null,
          function() { return implode("", func_get_args()); }
        ),

        "CLOSE_P" => new Ferno\Loco\StringParser("</p>"),

        "UTF-8 CHAR" => new Ferno\Loco\LazyAltParser(
          array(
            new Ferno\Loco\Utf8Parser(array("<", ">", "&")),                         // match any UTF-8 character except <, > or &
            new Ferno\Loco\StringParser("&lt;",  function($string) { return "<"; }), // ...or an escaped < (unescape it)
            new Ferno\Loco\StringParser("&gt;",  function($string) { return ">"; }), // ...or an escaped > (unescape it)
            new Ferno\Loco\StringParser("&amp;", function($string) { return "&"; })  // ...or an escaped & (unescape it)
          )
        ),
      )
    );
  
    $p->parse("<p>Your text here &amp; here &amp; &lt;here&gt;</p>");
    // returns "Your text here & here & <here>"









-----
This documentation is available via MIT License, and is (C) Copyright 2012-2020 Ferno (QNTM).
