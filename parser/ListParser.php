<?php
 
require_once('Parser.php');
 
class ListParser extends Parser {

    // private $allCommands = array(ListLexer::BEGIN, ListLexer::END, ListLexer::IF, ListLexer::ELSE
    //     , ListLexer::END, ListLexer::FOREACH);

    public function ListParser(Lexer $input) {
        parent::__construct($input);
    }
 
    /** list : '[' elements ']' ; // match bracketed list */
    public function rlist() {
        $this->match(ListLexer::BEGIN);
        $this->expr();
        $this->match(ListLexer::END);
    }
    /**  expr = HTMLCODE* | COMMAND */
    function expr() {
        if ($this->lookahead->type == ListLexer::HTMLCODE ) {
            while ($this->lookahead->type == ListLexer::HTMLCODE) {
                $this->match(ListLexer::HTMLCODE);
            }
                
        }
    }
    /** element : name | list ; // element is name or nested list */
    function element() {
        if ($this->lookahead->type == ListLexer::NAME ) {
            $this->match(ListLexer::NAME);
        }
        else if ($this->lookahead->type == ListLexer::LBRACK) {
            $this->rlist();
        }
        else {
            throw new Exception("Expecting name or list : Found "  . 
                                 $this->lookahead);
        }
    }
}
 
?>