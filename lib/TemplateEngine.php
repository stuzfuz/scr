<?php

class TemplateEngine {

    private function traverseAstForEach($ast, $level, $data, &$html) {
        // \Logger::logDebug(var_dump($ast));
        // \Logger::logDebug( "\n" . str_pad("", $level * 3) . "  FOREACHNODE  FOREACH\n");
        // \Logger::logDebug( "FOREACH forvariable = " . $ast["forvariable"]);

        if (!isset($ast['forvariable'])) {
            die("'traverseAstForEach'  Ano forvariable set :-((");
        }
        $forvariable = strtolower($ast['forvariable']);
        // \Logger::logDebug( "\n'traverseAstForEach'  found variable = $forvariable\n");

        if (!isset($data[$forvariable])) {
            die("\n'traverseAstForEach'   forvariable  $forvariable NOT found in 'data'");
        }

        $arr = $data[$forvariable];

        foreach ($arr as $entry) {
            traverseAST($ast["fortemplate"], $level+1, $entry, $html);
            $html  .= "\n";
        }
    }

    private function traverseAstIf($ast, $level, $data, &$html) {      
        if (!isset($ast['ifvariable'])) {
            die("no ifvariable set :-((");
        }
        $variable = strtolower($ast['ifvariable']);
        // \Logger::logDebug( "\nIF found variable = $variable\n");

        if (!isset($data[$variable])) {
            // \Logger::logDebug("\nIF  variable  $variable NOT found in 'data'");
        }

        if (!isset($data["IFTRUE"])) {
            // \Logger::logDebug("\nIF  could not find a IFTRUE in  'data'");
        }

        if ($data[$variable]) {
            traverseAST($ast["IFTRUE"], $level+1, $data, $html);
            $html  .= "\n";
        } else if (!isset($data["IFFALSE"])) {
            traverseAST($ast["IFFALSE"], $level+1, $data, $html);
            $html  .= "\n";
        }    
    }

    private function traverseAST($ast, $level, $data, &$html) {
        foreach ($ast as $key => $node) {
            if ($key === "FOREACH") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) . " FOREACH\n");
                traverseAstForEach($node, $level+1, $data, $html);
            } else if ($key === "IF") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." IF\n");
                traverseAstIf($node, $level+1, $data, $html);
            } else if ($key === "IFTRUE") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." IFTRUE\n");
                die("this should be handled by  'traverseAstIf' ");
            } else if ($key === "IFFALSE") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." IFFALSE\n");
                die("this should be handled by  'traverseAstIf' ");
            } else if ($key === "ifvariable") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." ifvariable\n");
                die("this should be handled by  'traverseAstIf' ");
            } else if ($key === "forvariable") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." forvariable\n");
                die("this should be handled by  'traverseAstForEach' ");
            } else if ($key === "ELSE") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." ELSE\n");
                die("this should be handled by  'traverseAstIf' ");
            // } // else if ($key === "ELSE") {
            //     echo "\n" . str_pad("", $level * 3) ." HTML\n";
            //     $html .= $node->txt . "\n"; 
            } else  {
                // HTMLCODE or VARIABLE
                print_r($node);
                if ($node["name"] ==="HTMLCODE") {
                    // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." HTMLCODE\n");
                    $html .= $node["text"];
                } else if ($node["name"] === "VARIABLE") {
                    $variablename = strtolower(  $node["text"]);
                    // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." VARIABLE   name = $variable \n");

                    if (!isset($data[$variablename])) {
                        die("'traverseAST()'  variablename '$variablename' not set in 'data' ");
                    }
                    $html .= $data[$variablename];

                } else {
                    // \Logger::logDebug( "node which lead to die()\n\n", var_dump($node)); 
                    die("well - we should never end up here :-(");
                }
            }
        }
    }

    private static function findTemplateByName(string $tmpl, string $tmplname) : int  {
        // $searchString = "<!-- ###" . $tmplname . "###";
        return  strpos($tmpl, $tmplname);
    }

    private static function getTemplateCode(string $tmpl)  {
        $start = \ApplicationConfig::$BEGIN;
        $end =  \ApplicationConfig::$END;
        $pattern = '/' . preg_quote($start) . '(.*';
        $pattern .= ')' . preg_quote($end) . '/s';
        // \Util::my_var_dump( $pattern, "TemplateEngine::find_between()   pattern" );
        $i = preg_match($pattern, $tmpl, $matches);
        $code = null;
        if ($i) {
            $code = $matches[0];
        }
        return $code;
    }

    private static function find_between_all($string, $start, $end) {
        $pattern = '/' . preg_quote($start) . '(.*?';
        $pattern .= ')' . preg_quote($end) . '/s';
        // \Util::my_var_dump( $pattern, "TemplateEngine::find_between()   pattern" );
        $i = preg_match_all($pattern, $string, $matches);
        $string = null;
        if ($i) {
            $code = array();
            foreach($matches[1] as $m) {
                // \Util::my_var_dump( htmlspecialchars(  $m), "TemplateEngine::find_between_all()   m = " );
                // $m = substr($m, strlen($start));
                // $m = substr($m, 0, -strlen($end) + 1);
                $code[] = $m;
            }
            $string = implode ( "\n" , $code );
        }

        //   $matches[0];
    
        return $string;
    }
   
    private static function renderTemplate(string $tmplFilename, $data) : string {
        $partial = file_get_contents ($tmplFilename);
        if (!$partial) {
            \Logger::logError("TemplateEngine::render()   could not open file : " , $tmplFilename);
            readfile('static/500.html');
            exit();
        }
        return self::renderTemplateString($partial, $data);
    } 

    private static function getPartials(string $template) {
        $start = \ApplicationConfig::$PARTIALBEGIN;
        $end = \ApplicationConfig::$PARTIALEND;

        $pattern = '/' . preg_quote($start) . '(.*?';
        $pattern .= ')' . preg_quote($end) . '/s';
        // \Util::my_var_dump( $pattern, "TemplateEngine::find_between()   pattern" );
        $i = preg_match_all($pattern, $template, $matches);
        $string = null;
        if ($i) {
            // \Logger::logDebug("getPartials()  [".  __LINE__  . "]  matches  = ",$matches);
            $code = array();
            foreach($matches[1] as $m) {
                // \Util::my_var_dump( htmlspecialchars(  $m), "TemplateEngine::find_between_all()   m = " );
                // $m = substr($m, strlen($start));
                // $m = substr($m, 0, -strlen($end) + 1);
                // \Logger::logDebug("getPartials()  [".  __LINE__  . "]  m  = ",$m);
                $code[] = $m;
            }
        }
        return $code;
    }

    private static function renderTemplateString(string $html, $data) : string {
        var_dump($html);
        $template = '';

        \Logger::logDebug("renderPartialString() [".  __LINE__  . "]  html = ", $html);

        $templateBegin = self::findTemplateByName($html,\ApplicationConfig::$TEMPLATEBEGIN);
        if ($templateBegin !== 0) {
            $templateEnd = self::findTemplateByName($html,\ApplicationConfig::$TEMPLATEEND);
            if ($templateEnd === 0) {
                \Util::quit500("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEEND . " not found in file " ,  $tmplFilename);
            }
            // well - there it is: subtract 2 and it works
            $template = substr($html, $templateBegin + strlen(\ApplicationConfig::$TEMPLATEBEGIN), $templateEnd - $templateBegin  - strlen(\ApplicationConfig::$TEMPLATEEND)-2);
        } else {
            \Logger::logDebug("renderPartialString()  [".  __LINE__  . "]  could not find a  " . \ApplicationConfig::$TEMPLATEBEGIN  ." in the template = ", $template);
            \Util::quit500("Fatal Error - renderPartialString()  [".  __LINE__  . "]  could not find a  " . \ApplicationConfig::$TEMPLATEBEGIN  ." in the template = ", $template);

        }
        \Logger::logDebug("renderPartialString()  [".  __LINE__  . "]  template  = ",$template);

        $partials = self::getPartials($template);

        if ($partials != null) {
            foreach ($partials as $partial) {
                \Logger::logDebug("renderPartialString() [".  __LINE__  . "]  partial   = ", $partial);
            }
        }
        \Logger::logDebug("renderPartialString() [".  __LINE__  . "]     partials.length   = ", count($partials));

        // if ($templateCode != null) {
        //     echo "<br/><br/><br/>";
        //     \Util::my_var_dump( htmlspecialchars($templateCode), "renderPartialString()    templateCode from partial   = ");
        //     echo "<br/><br/><br/>";
        
        //     while ($templateCode!== null) {
        //         echo "<br/><br/><br/>";
        //         \Util::my_var_dump( htmlspecialchars($templateCode), "renderPartialString()   replacing tthis with 'REPLACED'   = ");
        //         echo "<br/><br/><br/>";
                
        //         $tmp = self::find_between_all($templateCode, "<!-- ", " -->");
                
        //        
                
        //         // $lexer = new TemplateLexer($tmp);
        //         // $parser = new TemplateParser($lexer);
        //         // $res = $parser->parseTemplate();

        //         \Logger::logDebug("renderPartialString()   partial res = ", var_dump($res));
                
        //         $partial = str_replace($templateCode, "REPLACED", $partial);
    
        //         echo "<br/><br/><br/>";
        //         // \Util::my_var_dump( htmlspecialchars($partial), "renderPartialString()   partial now   = ");
        //         echo "<br/><br/><br/>";
   
        //         $templateCode = self::getTemplateCode($partial);

        //         // echo "<br/><br/><br/>";
        //         // \Util::my_var_dump( htmlspecialchars($templateCode), "renderPartialString()   templateCode now   = ");
        //         // echo "<br/><br/><br/>";

        //     }
        // }
       
        // echo "<br/><br/><br/>";
        //         \Util::my_var_dump( htmlspecialchars($partial), "renderPartialString()   final partial returning   = ");
        //         echo "<br/><br/><br/>";

        return $template;
    } 


    // template names can be null -> no string type  
    public static function render(string $tmplFilename, $data,  $headertemplate,  $contenttemplate,  $footertemplate) : string {
        $template = file_get_contents ( $tmplFilename);
        if (!$template) {
            \Logger::logError("TemplateEngine::render()   could not open file : " , $tmplFilename);
            readfile('static/500.html');
            exit();
        }

        // check if there is a header, footer or content template
        // if ($headertemplate !== null) {
        //     $headerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEHEADER);
        //     if  ($headerBegin !== FALSE) {
        //         // echo "<br> found a '###TEMPLATE_HEADER###'";
        //         // echo "<br><br> template BEFORE replacing = " . htmlspecialchars($template) . "<br><br>";
        //         $template = str_replace(\ApplicationConfig::$TEMPLATEHEADER, self::renderTemplate($headertemplate, $data), $template);
        //         // echo "<br><br> template AFTER replacing = " . htmlspecialchars($template) . "<br><br>";
        //     }
        // }
        
        if ($contenttemplate !== null) {
            $contentBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATECONTENT);
            if  ($contentBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_CONTENT###'";
                $template = str_replace(\ApplicationConfig::$TEMPLATECONTENT, self::renderTemplate($contenttemplate, $data), $template);
            }
        }

        // if ($footertemplate !== null) {
        //     $footerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEFOOTER);
        //     if  ($footerBegin !== FALSE) {
        //         // echo "<br> found a '###TEMPLATE_FOOTER###'";
        //         $template = str_replace(\ApplicationConfig::$TEMPLATEFOOTER, self::renderTemplate($footertemplate, $data), $template);

        //     }
        // }
        // file_put_contents('tmp.html', $template);
        return $template;
    }
}
