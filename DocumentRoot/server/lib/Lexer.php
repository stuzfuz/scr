<?php
 
abstract class Lexer {
 
    const EOF       = -1; // represent end of file char
    const EOF_TYPE  = 1;  // represent EOF token type
    protected $input;     // input string
    protected $p = 0;     // index into input of current character
    protected $c;         // current character
 
    public function __construct($input) {
        $this->input = $input;
        // prime lookahead
        $this->c = substr($input, $this->p, 1);
    }
 
    /** Move one character; detect "end of file" */
    public function consume() {
        $this->p++;
        if ($this->p >= strlen($this->input)) {
            $this->c = Lexer::EOF;
        }
        else {
            $this->c = substr($this->input, $this->p, 1);
        }
       // echo "'consume()'  position in string  p = $this->p  old c = $old    new c = $this->c   ord(c) ". ord($this->c) .  " \n\n";
    }
     /** Move one character; detect "end of file" */
     public function moveBack() {
        $this->p--;
        if ($this->p >= strlen($this->input)) {
            $this->c = Lexer::EOF;
        }
        else {
            $this->c = substr($this->input, $this->p, 1);
        }
    }
 
    public abstract function nextToken();
    public abstract function getTokenName($tokenType);
}
 
?>