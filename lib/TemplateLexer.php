<?php
 
 // https://www.codediesel.com/php/building-a-simple-parser-and-lexer-in-php/

// require_once('lexer.php');

class TemplateLexer extends Lexer {
    const HTMLCODE      = 2;
    const BEGIN    = 3;
    const END    = 4;
    const IF    = 5;
    const ELSE    = 6;
    const COMMAND   = 7; 
    const FOREACH   = 8; 
    const VARIABLE   = 9; 
    const BEGINTEMPLATE   = 10; 
    const ENDTEMPLATE   = 11; 

    static $tokenNames = array("n/a", "<EOF>",
                                "HTMLCODE","BEGIN", "END",
                               "IF", "ELSE", "COMMAND", "FOREACH", "VARIABLE");
 
    public function getTokenName($x) {
        return TemplateLexer::$tokenNames[$x];
    }
 
    public function TemplateLexer($input) {
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

    public function isLetterOrUnderscore() {
        return ($this->c >= 'a' &&
               $this->c <= 'z' ||
               $this->c >= 'A' &&
               $this->c <= 'Z' || $this->c == '_' );
    }

    public function isLETTERorNUMBER() {
        return ($this->c >= 'a' &&
               $this->c <= 'z' ||
               $this->c >= 'A' &&
               $this->c <= 'Z') || ( $this->c <= '9' && $this->c >= '0');
    }
    
    public function isAnyCharacter() {
        return ($this->c >= 'a' &&
               $this->c <= 'z' ||
               $this->c >= 'A' &&
               $this->c <= 'Z') || ( $this->c <= '9' && $this->c >= '0') ||
               $this->c == '>' ||
               $this->c == '<' ||
               $this->c == '(' ||
               $this->c == ')' ||
               $this->c == '/' ||
               $this->c == '"' ||
               $this->c == '=' ||
               $this->c == '-' ||
               $this->c == '\'' ||
               $this->c == '_' ||
               $this->c == '\\' ||
               $this->c == '+' ||
               $this->c == ',' ||
               $this->c == ':' ||
               $this->c == ';' ||
               $this->c == '[' ||
               $this->c == ']' ||
               $this->c == '#' ||
               $this->c == '.' ||
               $this->c == '?' ||
               $this->c == '!' ||
               $this->c == '|' ||
               $this->c == ' ' ||
               $this->c == '\t' ||
               $this->c == '!';
    }

    public function isHash() {
        return ($this->c == '#');
    }

    public function isAt() {
        return ($this->c == '@');
    }
 
    public function nextToken() {
        while ( $this->c != self::EOF ) {
            switch ( $this->c ) {
                // case ' ' :  
                // case '\t': 
                case '\n': 
                case Chr(10):
                case '\r': $this->WS();
                           continue;
                case '#' : if ($this->isHash()) return $this->COMMAND();
                case '@' : if ($this->isAt()) return $this->VARIABLE();
                
               default:
                    if ($this->isAnyCharacter() ) return $this->HTMLCODE();
                    throw new Exception("invalid character: " + $this->c);
            }
        }
        return new Token(self::EOF_TYPE,"<EOF>");
    }

    /** NAME : ('a'..'z'|'A'..'Z')+; // NAME is sequence of >=1 letter */
    public function COMMAND() {
        // echo "\n in COMMAND()  ch = " . $this->c;
        $buf = '';
        $var = '';
        if ($this->isHash()) {
            $this->consume();
        }
        if ($this->isHash()) {
            $this->consume();
        }
        if ($this->isHash()) {
            $this->consume();
        }
        // echo "\n in COMMAND()  after first 3# ch = " . $this->c;

        do {
            $buf .= $this->c;
            // echo "\n in COMMAND() reading COmmand name   c  = " . $this->c . "\n";
            $this->consume();
        } while ($this->isLETTER());
        
        // echo "COMMAND() after reading the COMMAND name   buf = $buf    this->c =  $this->c \n\n" ;
        
        if ($this->isOpenParenthesis()) {
            echo "in IF openParent()      c =  $this->c \n\n" ;
            $this->consume();
            do {
                echo "in IF  in DO     openParent()      c =  $this->c \n\n" ;
                $var .= $this->c;
                $this->consume();
            } while ($this->isLetterOrUnderscore());
            echo "in IF  AFTER  DO     closeing parenthesis()      c =  $this->c \n\n" ;
            if (!$this->isCloseParenthesis()) {
                throw new Exception("parenthis has to be close: " + $this->c);
            }
            $this->consume();
        } 
        // echo "after IF openParent()      c =  $this->c \n\n" ;
        if (!$this->isHash()) {
            throw new Exception("command must be close with ###: " + $this->c);
        } else {
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
            // case "ENDIF": return new Token(self::ENDIF, "ENDIF: " . $buf . ";   var: " . $var);
            case "FOREACH": return new Token(self::FOREACH, "FOREACH: " . $buf . ";   var: " . $var);
            case "BEGIN": return new Token(self::BEGIN, "BEGIN: " . $buf . ";   var: " . $var);
            case "END": return new Token(self::END, "END: " . $buf . ";   var: " . $var);
            // case "BEGINTEMPLATE": return new Token(self::BEGINTEMPLATE, "BEGINTEMPLATE: " . $buf . ";   var: " . $var);
            // case "ENDTEMPLATE": return new Token(self::ENDTEMPLATE, "ENDTEMPLATE: " . $buf . ";   var: " . $var);
        }
        echo "\n unknown command: $buf\n";
        throw new Exception("unknown command found '$buf'   : " + $this->c);
    }

    /** VARIABLE : ('a'..'z'|'A'..'Z')+; // VARIABLE is sequence of >=1 letter */
    public function VARIABLE() {
        $buf = '';
        if ($this->isAt()) {
            $this->consume();
        }
        if ($this->isAt()) {
            $this->consume();
        } 
        do {
            $buf .= $this->c;
            //  echo "\n in VARIABLE() reading variable name   c  = " . $this->c . "\n";
            $this->consume();
            // die();
        } while ($this->isLETTERorNUMBER());
        
        if ($this->isAt()) {
            $this->consume();
        }
        if ($this->isAt()) {
            $this->consume();
        }
        return new Token(self::VARIABLE, $buf);
    }



    /** HTMLCODE :  */
    public function HTMLCODE() {
        // echo "\n HTMLCODE()   c = $this->c \n";
        $buf = '';
        do {
            // echo "\n HTMLCODE()  do-while  c = $this->c \n";
            if ($this->c == '#') {
                $buf .= $this->c;
                $this->consume(); 
                if ($this->c == '#') {
                    $this->moveBack();
                    // $this->moveBack();
                    // die();
                    $buf = substr($buf, 0, strlen($buf)-1);
                    // echo "'HTMLCODE()'   early exit buf = '$buf'";
                    return new Token(self::HTMLCODE, $buf);
                }
            }
            $buf .= $this->c;
            $this->consume();
        } while ($this->isAnyCharacter());      // isAnyCharacter
        // echo "\n normal exit from HTMLCODE()";
        return new Token(self::HTMLCODE, $buf);
    }
 
    /** WS : (' '|'\t'|'\n'|'\r')* ; // ignore any whitespace */
    public function WS() {
        while(ctype_space($this->c)) {
            $this->consume();
        }
    }
}
 
?>