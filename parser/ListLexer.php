<?php
 
 // https://www.codediesel.com/php/building-a-simple-parser-and-lexer-in-php/

 
require_once('lexer.php');
 
class ListLexer extends Lexer {
    const NAME      = 2;
    const COMMA     = 3;
    const LBRACK    = 4;
    const RBRACK    = 5;
    const BEGIN    = 6;
    const END    = 7;
    const IF    = 8;
    const ELSE    = 9;
    const ENDIF    = 10;
    const COMMAND   = 11; 
    const FOREACH   = 12; 
    static $tokenNames = array("n/a", "<EOF>",
                                "NAME", "COMMA",
                               "LBRACK", "RBRACK", "BEGIN", "END",
                               "IF", "ELSE", "ENDIF", "COMMAND", "FOREACH" );
 
    public function getTokenName($x) {
        return ListLexer::$tokenNames[$x];
    }
 
    public function ListLexer($input) {
        parent::__construct($input);
    }

    
    public function isOpenParenthesis() {
        return ($this->c == '(');
    }

    public function isCloseParenthesis() {
        return ($this->c == ')');
    }
 
    public function isLETTER() {
        return ($this->c >= 'a' &&
               $this->c <= 'z' ||
               $this->c >= 'A' &&
               $this->c <= 'Z')  ;
    }

    public function isHash() {
        return ($this->c == '#');
    }
 
    public function nextToken() {
        while ( $this->c != self::EOF ) {
            switch ( $this->c ) {
                case ' ' :  
                case '\t': 
                case '\n': 
                case '\r': echo "\ncalling WS()\n"; $this->WS();
                           continue;
                case ',' : $this->consume();
                           return new Token(self::COMMA, ",");
                case '[' : $this->consume();
                           return new Token(self::LBRACK, "[");
                case ']' : $this->consume();
                           return new Token(self::RBRACK, "]");

                case '#' : if ($this->isHash()) return $this->COMMAND();
                
               default:
                    if ($this->isLETTER() ) return $this->NAME();
                    throw new Exception("invalid character: " + $this->c);
            }
        }
        return new Token(self::EOF_TYPE,"<EOF>");
    }

    /** NAME : ('a'..'z'|'A'..'Z')+; // NAME is sequence of >=1 letter */
    public function COMMAND() {
        // // echo "<br> in COMMAND()  ch = " . $this->c;
        $buf = '';
        $var = '';
        //$buf .= $this->c;
        if ($this->isHash()) {
            //7$buf .= $this->c;
            $this->consume();
        }
        if ($this->isHash()) {
            //$buf .= $this->c;
            $this->consume();
        }
        if ($this->isHash()) {
            //$buf .= $this->c;
            $this->consume();
        }
        // // echo "<br> in COMMAND()  after first 3# ch = " . $this->c;

        do {
            $buf .= $this->c;
            // // echo "<br> in COMMAND() readin COmmand name   c  = " . $this->c;
            $this->consume();
        } while ($this->isLETTER());
        // echo "COMMAND() after reading the COMMAND name   buf = $buf    this->c =  $this->c \n\n" ;
        if ($this->isOpenParenthesis()) {
            // echo "in IF openParent()      c =  $this->c \n\n" ;
            $this->consume();
            $var = $this->c;
            $this->consume();
            do {
                // echo "in IF  in DO     openParent()      c =  $this->c \n\n" ;
                $var .= $this->c;
                $this->consume();
            } while ($this->isLETTER());
            // echo "in IF  AFTER  DO     closeing parenthesis()      c =  $this->c \n\n" ;
            if (!$this->isCloseParenthesis()) {
                throw new Exception("parenthis has to be close: " + $this->c);
            }
            $this->consume();
        } 
        if ($this->isHash()) {
            //$buf .= $this->c;
            $this->consume();
        }
        if ($this->isHash()) {
            //$buf .= $this->c;
            $this->consume();
        }
        if ($this->isHash()) {
            //$buf .= $this->c;
            $this->consume();
        }
        switch ($buf) {
            case "IF": return new Token(self::IF, "IF: " . $buf . ";   var: " . $var);
            case "ELSE": return new Token(self::ELSE, "ELSE: " . $buf . ";   var: " . $var);
            case "ENDIF": return new Token(self::ENDIF, "ENDIF: " . $buf . ";   var: " . $var);
            case "FOREACH": return new Token(self::FOREACH, "FOREACH: " . $buf . ";   var: " . $var);
            case "BEGIN": return new Token(self::BEGIN, "BEGIN: " . $buf . ";   var: " . $var);
            case "END": return new Token(self::END, "END: " . $buf . ";   var: " . $var);
        }
        return new Token(self::COMMAND, "command: " . $buf . ";   var: " . $var);
    }

    /** NAME : ('a'..'z'|'A'..'Z')+; // NAME is sequence of >=1 letter */
    public function NAME() {
        $buf = '';
        do {
            $buf .= $this->c;
            $this->consume();
        } while ($this->isLETTER());
 
        return new Token(self::NAME, $buf);
    }
 
    /** WS : (' '|'\t'|'\n'|'\r')* ; // ignore any whitespace */
    public function WS() {
        echo "\n WS() \n";
        while(ctype_space($this->c)) {
            $this->consume();
        }
    }
}
 
?>