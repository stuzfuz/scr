<?php
 
require_once('TemplateLexer.php');
require_once('Token.php');
require_once('TemplateParser.php');
 


$data = array();

$date1 = "17.3.1977";
$date2 = "17.4.1978";

$day["days"] =  

$msg1['username'] = '@donalduck';
$msg1['txt'] = 'Lorem ipsum und so weiter';
$msg1['wichtig'] = false;

$msg2['username'] = '@dagobertduck';
$msg2['txt'] = 'ein adneres lorem ipsum und so weiter';
$msg2['wichtig'] = true;


$msg3['username'] = '@daisyduck';
$msg3['txt'] = 'schubidududu ein adneres lorem ipsum und so weiter';
$msg3['wichtig'] = false;


$msg4['username'] = '@goofy';
$msg4['txt'] = 'oin oink schubidududu ein adneres lorem ipsum und so weiter';
$msg4['wichtig'] = true;


$day1["msgs"][] = $msg1;
$day2["msgs"][] = $msg2;
$day2["msgs"][] = $msg3;
$day2["msgs"][] = $msg4;

$day1["date"] = $date1;
$day2["date"] = $date2;
$days = array($day1, $day2);

$data["days"] = $days;

// var_dump($data);
print_r($data);

$s = file_get_contents('test.txt');
echo "s = " . var_dump($s);
$lexer = new TemplateLexer($s);
$parser = new TemplateParser($lexer);
$parser->parseTemplate($data); // begin parsing at rule list
 
echo "\n\n DONE \n\n";
?>