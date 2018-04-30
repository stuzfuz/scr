<?php
 
require_once('TemplateLexer.php');
require_once('Token.php');
require_once('TemplateParser.php');
 
$s = file_get_contents('test_parse.txt');
echo "s = " . var_dump($s);
$lexer = new ListLexer($s);
$parser = new ListParser($lexer);
$parser->rlist(); // begin parsing at rule list
 
echo "\n\n DONE \n\n";
?>