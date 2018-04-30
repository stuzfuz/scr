<?php

class Symbols {
    public const syBegin = 1;
    public const syEnd = 2;

    public const syForEach = 3;
    public const syForEachEnd = 4;

    public const syIf = 5;
    public const syIfEnd = 6;
    public const syElse = 7;
    
    public const syVariable = 8;

    public const syPlaceholder = 9;

    public const syCommentOpen = 10;
    public const syCommentClose = 11;

    
    public const syEOF = 12;

    public const syLeftPar = 13;
    public const syRightPar = 14;

    public const syMinus = 15;
    public const syExcl = 16;

    public const sy3Hash = 17;


    private function __construct() {
    }
}

class TemplateParser {
    private $templateCode = null;

    private $symb = null; 
    private $identifier = null;
    private $placeholder = null;

    private $chEOF = 0;

    private $syCnr = -1;

    private $ch = null;

    public function __construct(string $templateCode) {
        $this->templateCode = $templateCode;
        self::newCh();
        self::newSy
        ();
    }

    private function newCh() {
        if ($this->syCnr < strlen($this->templateCode)) {
            $this->syCnr++;
            $this->ch = substr($this->templateCode, $this->syCnr, 1);
        } else {
            $this->ch = $this->chEOF;
        }
    }

    private function isCharacter()
    private function newSy() {
        while (ctype_space ( $this->ch)) {
            self::newCh();
        }
        
        if ($this->ch >= 'A') || ($this->ch <= 'Z') ||  {
            case '<':       $this->sy  = Symbols::syLeftPar; 
                            break;
            case '>':       $this->sy  = Symbols::syRightPar; 
                            break;
            case '!':       $this->sy  = Symbols::syExcl; 
                            break;
            case '-':       $this->sy  = Symbols::syMinus; 
                            break;
        }
    }


    
}


$filename ="test1.html";
$s = file_get_contents($filename);

echo "<br> reading file: $filename";
echo "<br> <br> contents of $filename";
var_dump(htmlspecialchars($s));
echo "<br><br>";

$parser = new TemplateParser($s);

echo "<br><br> DONE <br><br>";