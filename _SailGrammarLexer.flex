package generated;

import com.intellij.lexer.FlexLexer;
import com.intellij.psi.tree.IElementType;

import static com.intellij.psi.TokenType.BAD_CHARACTER;
import static com.intellij.psi.TokenType.WHITE_SPACE;
import static generated.GeneratedTypes.*;

%%

%{
  public _SailGrammarLexer() {
    this((java.io.Reader)null);
  }
%}

%public
%class _SailGrammarLexer
%implements FlexLexer
%function advance
%type IElementType
%unicode

EOL=\R
WHITE_SPACE=\s+


%%
<YYINITIAL> {
  {WHITE_SPACE}              { return WHITE_SPACE; }

  "QUOTED_STRING"            { return QUOTED_STRING; }
  "BARE_WORD"                { return BARE_WORD; }
  "CODE_BLOCK"               { return CODE_BLOCK; }
  "CAMEL_CASE_IDENTIFIER"    { return CAMEL_CASE_IDENTIFIER; }
  "LINE_BEGIN_INDENT"        { return LINE_BEGIN_INDENT; }
  "EOL"                      { return EOL; }


}

[^] { return BAD_CHARACTER; }
