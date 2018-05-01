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



// $date1 = "17.3.1977";
// $date2 = "17.4.1978";

// $day["days"] =  

// $msg1['username'] = '@donalduck';
// $msg1['txt'] = 'Lorem ipsum und so weiter';
// $msg1['important'] = false;

// $msg2['username'] = '@dagobertduck';
// $msg2['txt'] = 'ein adneres lorem ipsum und so weiter';
// $msg2['important'] = true;


// $msg3['username'] = '@daisyduck';
// $msg3['txt'] = 'schubidududu ein adneres lorem ipsum und so weiter';
// $msg3['important'] = false;

// $msg4['username'] = '@goofy';
// $msg4['txt'] = 'oin oink schubidududu ein adneres lorem ipsum und so weiter';
// $msg4['important'] = true;


// $day1["msgs"][] = $msg1;
// $day2["msgs"][] = $msg2;
// $day2["msgs"][] = $msg3;
// $day2["msgs"][] = $msg4;

// $day1["date"] = $date1;
// $day2["date"] = $date2;
// $days = array($day1, $day2);

// $data["days"] = $days;


$channel1["name"] = "SWE";
$channel2["name"] = "SWE"; 

$data["channels"][] = $channel1;
$data["channels"][] = $channel2;

// var_dump($data);
// print_r($data);

$s = file_get_contents('test.txt');
echo "s = " . var_dump($s);
$lexer = new TemplateLexer(trim($s));
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
die();

function traverseAstForEach($ast, $level, $data, &$html) {
    echo "\n\n\n";
    var_dump($ast);
    echo "\n\n\n";
    echo "\n" . str_pad("", $level * 3) . "  FOREACHNODE  FOREACH\n";
    echo "FOREACH forvariable = " . $ast["forvariable"];

    if (!isset($ast['forvariable'])) {
        die("'traverseAstForEach'  no forvariable set :-((");
    }
    $forvariable = strtolower($ast['forvariable']);
    echo "\n'traverseAstForEach'  found variable = $forvariable\n";

    if (!isset($data[$forvariable])) {
        die("\n'traverseAstForEach'   forvariable  $forvariable NOT found in 'data'");
    }

    $arr = $data[$forvariable];

    foreach ($arr as $entry) {
        traverseAST($ast["fortemplate"], $level+1, $entry, $html);
        $html  .= "\n";
    }
}

function traverseAstIf($ast, $level, $data, &$html) {      
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
        traverseAST($ast["IFTRUE"], $level+1, $data, $html);
        $html  .= "\n";
    } else if (!isset($data["IFFALSE"])) {
        traverseAST($ast["IFFALSE"], $level+1, $data, $html);
        $html  .= "\n";
    }    
}

function traverseAST($ast, $level, $data, &$html) {
    foreach ($ast as $key => $node) {
        if ($key === "FOREACH") {
            echo "\n" . str_pad("", $level * 3) . " FOREACH\n";
            traverseAstForEach($node, $level+1, $data, $html);
        } else if ($key === "IF") {
            echo "\n" . str_pad("", $level * 3) ." IF\n";
            traverseAstIf($node, $level+1, $data, $html);
        } else if ($key === "IFTRUE") {
            echo "\n" . str_pad("", $level * 3) ." IFTRUE\n";
            die("this should be handled by  'traverseAstIf' ");
        } else if ($key === "IFFALSE") {
            echo "\n" . str_pad("", $level * 3) ." IFFALSE\n";
            die("this should be handled by  'traverseAstIf' ");
        } else if ($key === "ifvariable") {
            echo "\n" . str_pad("", $level * 3) ." ifvariable\n";
            die("this should be handled by  'traverseAstIf' ");
        } else if ($key === "forvariable") {
            echo "\n" . str_pad("", $level * 3) ." forvariable\n";
            die("this should be handled by  'traverseAstForEach' ");
        } else if ($key === "ELSE") {
            echo "\n" . str_pad("", $level * 3) ." ELSE\n";
            die("this should be handled by  'traverseAstIf' ");
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
                echo "\n" . str_pad("", $level * 3) ." VARIABLE   name = $variablename \n";

                if (!isset($data[$variablename])) {
                    die("'traverseAST()'  variablename '$variablename' not set in 'data' ");
                }
                $html .= $data[$variablename];

            } else {
                echo "\n\n\n\n";
                echo "node which lead to die()\n\n"; 
                var_dump($node);
                die("well - we should never end up here :-(");
            }
        }
    }
}

echo "\n\n --------------------------------";
echo "\n\n traversing the AST \n\n";
$html = '';
traverseAST($ast, 1, $data, $html);


echo "\n\n --------------------------------";
echo "final html =  ";
var_dump($html);

echo "\n\n -------------------xxxxxx-------------";
echo "\n\n ".  $ast["FOREACH"]["forvariable"]  . " \n\n";

echo "\n\n DONE \n\n";
?>