<?php
 
require_once('TemplateLexer.php');
require_once('Token.php');
 
$s = trim(file_get_contents('test.txt'));
echo "s = " . var_dump($s);

$lexer = new TemplateLexer($s);
$token = $lexer->nextToken();
 
$out = '';
while($token->type != 1) {
    echo "$token   \n";
    $out .= $token->text . "\n";
    $token = $lexer->nextToken();
}

file_put_contents("new.html", $out);

?>