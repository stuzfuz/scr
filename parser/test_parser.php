<?php
 declare(strict_types = 1);
 error_reporting(E_ALL);
 
 // ist anscheinend in centos defaultmäßig auf off
 ini_set('display_errors', 'on');
 setlocale(LC_MONETARY, 'de_AT');

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
// print_r($data);

$s = file_get_contents('test.txt');
echo "s = " . var_dump($s);
$lexer = new TemplateLexer($s);
$parser = new TemplateParser($lexer);
try {
    $ast = $parser->parseTemplate($data); // begin parsing at rule list

    // echo "the ast ...\n\n";
    // print_r($ast);
    // echo "\n\n";


} catch (Exception $e) {
    echo "\n\n exception while parsing ...\n";
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    //var_dump($e);
}


function traverseForEach333($ast, $level) {
    // var_dump($ast);
    
    echo "\n" . str_pad("", $level * 3) . "node:    FOREACH forvariable " . $ast["forvariable"]  ."\n";
    // echo "\n\ntraverseForEach()  \n\n";
    // print_r($ast);
    // for () ... {
        traverseAST($ast, $level + 1);
    // }
}

function traverseIf333($ast, $level) {
    echo "\n" . str_pad("", $level * 3) . "node:    IF ifvariable " . $ast["ifvariable"]  ."\n";

    // if  {
        traverseAST($ast["IFTRUE"], $level + 1);
    //  } else {
        traverseAST($ast["IFFALSE"], $level + 1);
    // }
}

function traverseAST____($ast, $level) {
    foreach ($ast as $key => $node) {
        echo "\n key = $key";
        // echo "\n\n is_array(node)" . is_array($node) . "\n";
        // echo "\n\n is_object(node)" . is_object($node) . "\n\n";

        if (is_int($key)) {
            echo "\n" . str_pad("", $level * 3) . "node:    TOKENTYP: " . $node["name"] . "  TOKENTEXT: ". $node["text"] ."\n";
        } else if(is_string($key)) {
            if ($key === "FOREACH") {
                echo "\n" . str_pad("", $level * 3) . "node:    FOREACH: \n";
                echo "\n\n\n";
                // print_r($node["FOREACH"]);
                // print_r($node["FOREACH"]["forvariable"]);
                // die();

                // print_r($node["FOREACH"]["forvariable"]);
                traverseForEach($node["FOREACH"], $level +1);
            } else if ($key === "IF") {
                echo "\n" . str_pad("", $level * 3) . "node:    IF: \n";
                // traverseForIF($node["IF"], $level +1);
            } else if ($key === "ifvariable") {
                echo "\n" . str_pad("", $level * 3) . "node:    IFVARIABLE: ". $node[$key].  " \n";
                // traverseForIF($node["IF"], $level +1);
            } else if ($key === "forvariable") {
                echo "\n" . str_pad("", $level * 3) . "node:    FORVARIABLE: ". $node[$key].  " \n";
                // traverseForIF($node["IF"], $level +1);
            } else {
                echo "\n key = $key  and thats not what we want ...\n\n";
                echo "\n node .... $node\n\n";
                // die("somethings not right  2222");
            }
        } else {
            // die("somethings not right");
        }
    
        
        // var_dump($node);
    }
}

function traverseForEach1($ast, $level) {
    echo "FOREACH forvariable = " . $ast["forvariable"];
    foreach ($ast as $key => $node) {
        if ($key === "FOREACH") {
            echo "\n" . str_pad("", $level * 3) . "  FOREACHNODE  FOREACH\n";
            traverseForEach1($node, $level+1);
        } else if ($key === "IF") {
            echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  IF\n";
            traverseIf1($node, $level+1);
        } else if ($key === "IFTRUE") {
            echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  IFTRUE\n";
            traverseAST1($node, $level+1);
        } else if ($key === "IFFALSE") {
            echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  IFFALSE\n";
            traverseAST1($node, $level+1);
        } else if ($key === "ifvariable") {
            echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  ifvariable\n";
        } else if ($key === "forvariable") {
            echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  forvariable\n";
        } else if ($key === "ELSE") {
            echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  ELSE\n";
        } else {
            echo "\n" . str_pad("", $level * 3) ."FOREACHNODE   HTML\n";
        }
        
        
        // echo "\n key = $key\n";
       
        
        // var_dump($node);
    }
}


function traverseIf1($ast, $level) {
    foreach ($ast as $key => $node) {
        if ($key === "FOREACH") {
            echo "\n" . str_pad("", $level * 3) . "  IFNODE   FOREACH\n";
            traverseForEach1($node, $level+1);
        } else if ($key === "IF") {
            echo "\n" . str_pad("", $level * 3) ." IFNODE   IF\n";
            traverseIf($node, $level+1);
        } else if ($key === "IFTRUE") {
            echo "\n" . str_pad("", $level * 3) ." IFNODE   IFTRUE\n";
            traverseAST1($node, $level+1);
        } else if ($key === "IFFALSE") {
            echo "\n" . str_pad("", $level * 3) ." IFNODE   IFFALSE\n";
            traverseAST1($node, $level+1);
        } else if ($key === "ifvariable") {
            echo "\n" . str_pad("", $level * 3) ." IFNODE   ifvariable\n";
        } else if ($key === "forvariable") {
            echo "\n" . str_pad("", $level * 3) ." IFNODE   forvariable\n";
        } else if ($key === "ELSE") {
            echo "\n" . str_pad("", $level * 3) ." IFNODE   ELSE\n";
        } else {
            echo "\n" . str_pad("", $level * 3) ."IFNODE    HTML\n";
        }
        
        
        // echo "\n key = $key\n";
       
        
        // var_dump($node);
    }
}


function traverseAST1($ast, $level) {
    foreach ($ast as $key => $node) {
        if ($key === "FOREACH") {
            echo "\n" . str_pad("", $level * 3) . " FOREACH\n";
            traverseForEach1($node, $level+1);
        } else if ($key === "IF") {
            echo "\n" . str_pad("", $level * 3) ." IF\n";
            traverseAST1($node, $level+1);
        } else if ($key === "IFTRUE") {
            echo "\n" . str_pad("", $level * 3) ." IFTRUE\n";
            traverseAST1($node, $level+1);
        } else if ($key === "IFFALSE") {
            echo "\n" . str_pad("", $level * 3) ." IFFALSE\n";
            traverseAST1($node, $level+1);
        } else if ($key === "ifvariable") {
            echo "\n" . str_pad("", $level * 3) ." ifvariable\n";
        } else if ($key === "forvariable") {
            echo "\n" . str_pad("", $level * 3) ." forvariable\n";
        } else if ($key === "ELSE") {
            echo "\n" . str_pad("", $level * 3) ." ELSE\n";
        } else {
            echo "\n" . str_pad("", $level * 3) ." HTML\n";
        }
        
        
        // echo "\n key = $key\n";
       
        
        // var_dump($node);
    }
}

echo "\n\n --------------------------------";
echo "\n\n traversing the AST \n\n";
traverseAST1($ast, 1);


echo "\n\n -------------------xxxxxx-------------";
echo "\n\n ".  $ast["FOREACH"]["forvariable"]  . " \n\n";

echo "\n\n DONE \n\n";
?>