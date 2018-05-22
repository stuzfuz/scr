<?php
 
// require_once('Parser.php');
 
class TemplateParser extends Parser {

    private $allTokens = array();

    public function ListParser(Lexer $input) {
        parent::__construct($input);
    }
 
    /** template:   'BEGIN' expr() 'END'  */
    public function parseTemplate() {
        // echo "parseTemplate()   lookahead:    '" . $this->lookahead ."'\n\n";
        // $ast[] = $this->lookahead->asObject();
        $this->match(TemplateLexer::BEGIN);
        $this->allTokens[] = "BEGIN";
        $this->expr($ast);
        // $ast[] = $this->lookahead->asObject();
        $this->match(TemplateLexer::END);
        $this->allTokens[] = "END";

        // echo "allTokens ... \n\n";
        // var_dump($this->allTokens);

        return $ast; 
    }

    /**  expr = HTMLCODE*  | VARIABLE* | COMMAND */
    function expr(&$ast) {
        $this->allTokens[] = "EXPR";
        // echo "expr()   lookahead:    '" . $this->lookahead . "'\n\n";
        while ($this->lookahead->type == TemplateLexer::HTMLCODE  ||
            $this->lookahead->type == TemplateLexer::VARIABLE) {
            if ($this->lookahead->type == TemplateLexer::HTMLCODE) {
                $ast[] = $this->lookahead->asObject();
                $this->match(TemplateLexer::HTMLCODE);
                $this->allTokens[] = "HTMLCODE";
            } else {
                $ast[] = $this->lookahead->asObject();
                $this->match(TemplateLexer::VARIABLE);
                $this->allTokens[] = "VARIABLE";
            }
        } 
        if ($this->lookahead->type == TemplateLexer::IF) {
            $this->matchIf($ast["IF"]);
        } else if ($this->lookahead->type == TemplateLexer::FOREACH) {
            $this->matchForEach($ast["FOREACH"]);
        }
    }

    /** IF  : expr();  [ 'ELSE' expr] 'END'  */
    function matchIf(&$ast) {
        $this->allTokens[] = "IF";
        // $ast[] = $this->lookahead->asObject();
        $ast["ifvariable"] = $this->lookahead->variable;
        $this->match(TemplateLexer::IF);
        $this->expr($ast["IFTRUE"]);

        if ($this->lookahead->type == TemplateLexer::ELSE) {
            // $ast["IFFALSE"] = $this->lookahead;
            $this->match(TemplateLexer::ELSE);
            $this->allTokens[] = "ELSE";
            $this->expr($ast["IFFALSE"]);
        }
        // var_dump($this->allTokens);
        // $ast[] = $this->lookahead->asObject();
        $this->match(TemplateLexer::END);
        $this->allTokens[] = "END";
        $this->expr($ast["AFTERIF"]);
    }
    
    /** FOREACH  : expr(); 'END'  */
    function matchForEach(&$ast) {
        $this->allTokens[] = "FOREACH";
        $ast["forvariable"] = $this->lookahead->variable;
        $this->match(TemplateLexer::FOREACH);
        $this->expr($ast["fortemplate"]);
        // $ast[] = $this->lookahead->asObject();
        $this->match(TemplateLexer::END);
        $this->allTokens[] = "END";
        $this->expr($ast["AFTERFOREACH"]);
    }
}

?>
