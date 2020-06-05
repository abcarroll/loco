## About Loco

Loco was created because I wanted to let people use XHTML comments on my website, and I wanted to be able validate that XHTML in a flexible way, starting with a narrow subset of XHTML and adding support for more tags over time. I believed that writing a parsing library would be more effective and educational than hand-writing (and then constantly hand-rewriting) a parser.

Loco, together with the Loco grammar [examples/simpleComment.php](https://github.com/ferno/loco/blob/master/examples/simpleComment.php), fulfilled the first objective. These were kept in successful use for several years. Later, I developed [examples/locoNotation.php](https://github.com/ferno/loco/blob/master/examples/locoNotation.php) which things even simpler for me. However, there were some drawbacks:

* Grammars had to be instantiated every time the comment-submission PHP script ran, which was laborious and inelegant. PHP doesn't make it possible to retrieve the text content of a callback, so the process of turning Loco from a parser *library* into a true parser *generator* stalled.
* Lack of backtracking meant I had to be inordinately careful in describing my CFG so that it would be unambiguous and work correctly. This need for extra effort kind of defeated the point.
* As I now realise, one of the most important things to consider when parsing user input is generating meaningful error messages when parsing failed. Loco is sort of bad at this, and users found it difficult to create correct HTML to keep it pleased.
* PHP is *horrible*.

Before beginning the project, I also observed that PHP had no parser combinator library, and I decided to fill this niche. Again, I ran into some problems:

* I didn't actually know what the term "parser combinator" meant at the time. It is not "a parser made up from a combination of other parsers". It is "a function or operator which accepts one or more parsers as input and returns a new parser as output". You can see the term being misused several times above. There is still no parser combinator library for PHP, to my knowledge.
* I knew, and still know, barely anything about parsing in general.
* PHP is *horrible*.

Overall I would say that this project fulfilled my needs at the time, and if it fulfills yours now that is probably just a coincidence. I would exercise caution when using Loco, or inspecting its code.
