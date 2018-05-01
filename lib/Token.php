<?php
 
class Token {
    public $type;
    public $text;

    public function __construct($type, $text, $variable = null) {
        $this->type = $type;
        $this->text = $text;
        $this->variable = $variable;
    }
 
    public function __toString() {
        $tname = TemplateLexer::$tokenNames[$this->type];
        return "<'" . $this->text . "'," . $tname . "', '" . $this->variable ."'>";
    }
    public function asObject() {
        $ret["name"] = TemplateLexer::$tokenNames[$this->type];
        $ret["text"] = $this->text;
        $ret["variable"] = $this->variable;
        return $ret;
    }
}
 
?>
