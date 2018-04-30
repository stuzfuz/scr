<?php
 
require_once('ListLexer.php');
require_once('Token.php');
require_once('ListParser.php');
 
$s = file_get_contents('test.txt');
echo "s = " . var_dump($s);
$lexer = new ListLexer($s);
$parser = new ListParser($lexer);
$parser->rlist(); // begin parsing at rule list
 
?>