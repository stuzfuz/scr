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
$msg1['important'] = false;

$msg2['username'] = '@dagobertduck';
$msg2['txt'] = 'ein adneres lorem ipsum und so weiter';
$msg2['important'] = true;


$msg3['username'] = '@daisyduck';
$msg3['txt'] = 'schubidududu ein adneres lorem ipsum und so weiter';
$msg3['important'] = false;

$msg4['username'] = '@goofy';
$msg4['txt'] = 'oin oink schubidududu ein adneres lorem ipsum und so weiter';
$msg4['important'] = true;


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

    echo "the ast ...\n\n";
    print_r($ast);
    echo "\n\n";


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

function traverseForEach1($ast, $level, $data, &$html) {
    echo "\n\n\n";
    var_dump($ast);
    echo "\n\n\n";
    echo "\n" . str_pad("", $level * 3) . "  FOREACHNODE  FOREACH\n";
    echo "FOREACH forvariable = " . $ast["forvariable"];

    if (!isset($ast['forvariable'])) {
        die("'traverseForEach1'  Ano forvariable set :-((");
    }
    $forvariable = strtolower($ast['forvariable']);
    echo "\n'traverseForEach1'  found variable = $forvariable\n";

    if (!isset($data[$forvariable])) {
        die("\n'traverseForEach1'   forvariable  $forvariable NOT found in 'data'");
    }

    $arr = $data[$forvariable];

    foreach ($arr as $entry) {
        traverseAST1($ast["fortemplate"], $level+1, $entry, $html);
        $html  .= "\n";
    }


    // // foreach ($ast as $key => $node) {
    //     // if ($key === "FOREACH") {
            
    //         traverseForEach1($node, $level+1, $data, $html);
    // //     } else if ($key === "IF") {
    // //         echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  IF\n";
    // //         traverseIf1($node, $level+1, $data, $html);
    // //     } else if ($key === "IFTRUE") {
    //         echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  IFTRUE\n";
    //         traverseAST1($node, $level+1, $data, $html);
    //     } else if ($key === "IFFALSE") {
    //         echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  IFFALSE\n";
    //         traverseAST1($node, $level+1, $data, $html);
    //     } else if ($key === "ifvariable") {
    //         echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  ifvariable\n";
    //     } else if ($key === "forvariable") {
    //         echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  forvariable\n";
    //     } else if ($key === "ELSE") {
    //         echo "\n" . str_pad("", $level * 3) ." FOREACHNODE  ELSE\n";
    //     } else {
    //         echo "\n" . str_pad("", $level * 3) ."FOREACHNODE   HTML\n";
    //     }
    // }
}


function traverseIf1($ast, $level, $data, &$html) {
        
    if (!isset($ast['ifvariable'])) {
        die("no ifvariable set :-((");
    }
    $variable = strtolower($ast['ifvariable']);
    echo "\nIF found variable = $variable\n";

    if (!isset($data[$variable])) {
        echo("\nIF  variable  $variable NOT found in 'data'");
    }

    if (!isset($data["IFTRUE"])) {
        echo("\nIF  could not find a IFTRUE in  'data'");
    }

    if ($data[$variable]) {
        traverseAST1($ast["IFTRUE"], $level+1, $data, $html);
        $html  .= "\n";
    } else if (!isset($data["IFFALSE"])) {
        traverseAST1($ast["IFFALSE"], $level+1, $data, $html);
        $html  .= "\n";
    }    
}


function traverseAST1($ast, $level, $data, &$html) {
    foreach ($ast as $key => $node) {
        if ($key === "FOREACH") {
            echo "\n" . str_pad("", $level * 3) . " FOREACH\n";
            traverseForEach1($node, $level+1, $data, $html);
        } else if ($key === "IF") {
            echo "\n" . str_pad("", $level * 3) ." IF\n";
            traverseIf1($node, $level+1, $data, $html);
        } else if ($key === "IFTRUE") {
            echo "\n" . str_pad("", $level * 3) ." IFTRUE\n";
            die("this should be handled by  'traverseIf1' ");
        } else if ($key === "IFFALSE") {
            echo "\n" . str_pad("", $level * 3) ." IFFALSE\n";
            die("this should be handled by  'traverseIf1' ");
        } else if ($key === "ifvariable") {
            echo "\n" . str_pad("", $level * 3) ." ifvariable\n";
            die("this should be handled by  'traverseIf1' ");
        } else if ($key === "forvariable") {
            echo "\n" . str_pad("", $level * 3) ." forvariable\n";
            die("this should be handled by  'traverseForEach1' ");
        } else if ($key === "ELSE") {
            echo "\n" . str_pad("", $level * 3) ." ELSE\n";
            die("this should be handled by  'traverseIf1' ");
        // } // else if ($key === "ELSE") {
        //     echo "\n" . str_pad("", $level * 3) ." HTML\n";
        //     $html .= $node->txt . "\n"; 
        } else  {
            // HTMLCODE or VARIABLE
            print_r($node);
            if ($node["name"] ==="HTMLCODE") {
                echo "\n" . str_pad("", $level * 3) ." HTMLCODE\n";
                $html .= $node["text"];
            } else if ($node["name"] === "VARIABLE") {
                $variablename = strtolower(  $node["text"]);
                echo "\n" . str_pad("", $level * 3) ." VARIABLE   name = $variable \n";

                if (!isset($data[$variablename])) {
                    die("'traverseAST1()'  variablename '$variablename' not set in 'data' ");
                }
                $html .= $data[$variablename];
                
            } else {
                echo "\n\n\n\n";
                echo "node which lead to die()\n\n"; 
                var_dump($node);
                die("well - we should never end up here :-(");
            }
        }
        
        
        // echo "\n key = $key\n";
       
        
        // var_dump($node);
    }
}

echo "\n\n --------------------------------";
echo "\n\n traversing the AST \n\n";
$html = '';
traverseAST1($ast, 1, $data, $html);


echo "\n\n --------------------------------";
echo "final html =  ";
var_dump($html);

echo "\n\n -------------------xxxxxx-------------";
echo "\n\n ".  $ast["FOREACH"]["forvariable"]  . " \n\n";

echo "\n\n DONE \n\n";
?>