<?php
 
class Token {
    public $type;
    public $text;

    public function __construct($type, $text) {
        $this->type = $type;
        $this->text = $text;
    }
 
    public function __toString() {
        $tname = TemplateLexer::$tokenNames[$this->type];
        return "<'" . $this->text . "'," . $tname . ">";
    }
    public function asObject() {
        $ret["name"] = TemplateLexer::$tokenNames[$this->type];
        $ret["text"] = $this->text;
        $ret["variable"] = $this->variable;
        return $ret;
    }
}
 
?>
