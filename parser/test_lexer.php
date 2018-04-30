<?php
 
require_once('ListLexer.php');
require_once('Token.php');
 
$s = file_get_contents('test.txt');
echo "s = " . var_dump($s);

$lexer = new ListLexer($s);
$token = $lexer->nextToken();
 
while($token->type != 1) {
    echo $token . "\n";
    $token = $lexer->nextToken();
}
 
?>