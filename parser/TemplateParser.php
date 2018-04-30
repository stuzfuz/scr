<?php
 
require_once('Parser.php');
 
class ListParser extends Parser {

    // private $allCommands = array(ListLexer::BEGIN, ListLexer::END, ListLexer::IF, ListLexer::ELSE
    //     , ListLexer::END, ListLexer::FOREACH);

    private $allTokens = array();

    public function ListParser(Lexer $input) {
        parent::__construct($input);
    }
 
    /** template:   'BEGIN' expr() 'END'  */
    public function rlist() {
        echo "rlist()   lookahead:    '" . $this->lookahead ."'\n\n";
        $this->match(ListLexer::BEGIN);
        $this->allTokens[] = "BEGIN";
        $this->expr();
        $this->match(ListLexer::END);
        $this->allTokens[] = "END";

        echo "allTokens ... \n\n";
        var_dump($this->allTokens);
    }
    /**  expr = HTMLCODE*  | VARIABLE* | COMMAND */
    function expr() {
        $this->allTokens[] = "EXPR";
        echo "expr()   lookahead:    '" . $this->lookahead . "'\n\n";
        while ($this->lookahead->type == ListLexer::HTMLCODE  ||
            $this->lookahead->type == ListLexer::VARIABLE) {
            if ($this->lookahead->type == ListLexer::HTMLCODE) {
                $this->match(ListLexer::HTMLCODE);
                $this->allTokens[] = "HTMLCODE";
            } else {
                $this->match(ListLexer::VARIABLE);
                $this->allTokens[] = "VARIABLE";
            }
        } 
        if ($this->lookahead->type == ListLexer::IF) {
            $this->matchIf();
        } else if ($this->lookahead->type == ListLexer::FOREACH) {
            $this->matchForEach();
        }
    }
    /** IF  : expr();  [ 'ELSE' expr] 'END'  */
    function matchIf() {
        $this->allTokens[] = "IF";
        $this->match(ListLexer::IF);
        $this->expr();

        if ($this->lookahead->type == ListLexer::ELSE) {
            $this->match(ListLexer::ELSE);
            $this->allTokens[] = "IF";
            $this->expr();
        }
        var_dump($this->allTokens);
        $this->match(ListLexer::END);
        $this->allTokens[] = "END";
        $this->expr();
    }
    
    /** FOREACH  : expr(); 'END'  */
    function matchForEach() {
        $this->allTokens[] = "FOREACH";
        $this->match(ListLexer::FOREACH);
        $this->expr();
        $this->match(ListLexer::END);
        $this->allTokens[] = "END";
        $this->expr();
    }
}
 
?>