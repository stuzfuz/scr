<?php
 
// require_once('Parser.php');
 
class TemplateParser extends Parser {

    private $allTokens = array();

    public function ListParser(Lexer $input) {
        parent::__construct($input);
    }
 
    /** template:   'BEGIN' expr() 'END'  */
    public function parseTemplate() {
        echo "parseTemplate()   lookahead:    '" . $this->lookahead ."'\n\n";
        $this->match(TemplateLexer::BEGIN);
        $this->allTokens[] = "BEGIN";
        $this->expr();
        $this->match(TemplateLexer::END);
        $this->allTokens[] = "END";

        echo "allTokens ... \n\n";
        var_dump($this->allTokens);
    }

    /**  expr = HTMLCODE*  | VARIABLE* | COMMAND */
    function expr() {
        $this->allTokens[] = "EXPR";
        echo "expr()   lookahead:    '" . $this->lookahead . "'\n\n";
        while ($this->lookahead->type == TemplateLexer::HTMLCODE  ||
            $this->lookahead->type == TemplateLexer::VARIABLE) {
            if ($this->lookahead->type == TemplateLexer::HTMLCODE) {
                $this->match(TemplateLexer::HTMLCODE);
                $this->allTokens[] = "HTMLCODE";
            } else {
                $this->match(TemplateLexer::VARIABLE);
                $this->allTokens[] = "VARIABLE";
            }
        } 
        if ($this->lookahead->type == TemplateLexer::IF) {
            $this->matchIf();
        } else if ($this->lookahead->type == TemplateLexer::FOREACH) {
            $this->matchForEach();
        }
    }

    /** IF  : expr();  [ 'ELSE' expr] 'END'  */
    function matchIf() {
        $this->allTokens[] = "IF";
        $this->match(TemplateLexer::IF);
        $this->expr();

        if ($this->lookahead->type == TemplateLexer::ELSE) {
            $this->match(TemplateLexer::ELSE);
            $this->allTokens[] = "IF";
            $this->expr();
        }
        var_dump($this->allTokens);
        $this->match(TemplateLexer::END);
        $this->allTokens[] = "END";
        $this->expr();
    }
    
    /** FOREACH  : expr(); 'END'  */
    function matchForEach() {
        $this->allTokens[] = "FOREACH";
        $this->match(TemplateLexer::FOREACH);
        $this->expr();
        $this->match(TemplateLexer::END);
        $this->allTokens[] = "END";
        $this->expr();
    }
}

?>
