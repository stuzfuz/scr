<?php

class TemplateEngine {

    private static function traverseAstForEach($ast, $level, $data, &$html) {
        // \Logger::logDebug(var_dump($ast));
        // \Logger::logDebug( "\n" . str_pad("", $level * 3) . "  FOREACHNODE  FOREACH\n");
        // \Logger::logDebug( "FOREACH forvariable = " . $ast["forvariable"]);

        if (!isset($ast['forvariable'])) {
            \Logger::logDebugPrintR("traverseAstForEach() [".  __LINE__  . "]  no forvariable set :-(( = ", $data); 
            \Util::quit500("Fatal Error - 'traverseAstForEach'   [" . __LINE__ ."]   no forvariable set :-((" , "");           
        }
        $forvariable = strtolower($ast['forvariable']);
        // \Logger::logDebug( "\n'traverseAstForEach'  found variable = $forvariable\n");

        if (!isset($data[$forvariable])) {
            \Logger::logDebugPrintR("'traverseAstForEach' [" . __LINE__ ."]    forvariable  forvariable NOT found in 'data' ", $data); 
            \Util::quit500("Fatal Error - 'traverseAstForEach' [" . __LINE__ ."]    forvariable  forvariable NOT found in 'data' ", $data);
        }

        $arr = $data[$forvariable];
        // \Logger::logDebugPrintR("'traverseAstForEach' [" . __LINE__ ."]    count(arr)  =    ", count($arr)); 
        if (count($arr) > 0) {
            foreach ($arr as $entry) {
                self::traverseAST($ast["fortemplate"], $level+1, $entry, $html);
                $html  .= "\n";
            }
        }

        if (isset($ast["AFTERFOREACH"])) {
            self::traverseAST($ast["AFTERFOREACH"], $level, $data, $html, true );
        }
        // ohhh boy - that's an ugly hack. somehow there has to be a better way
        // to traverse the code after the IF [ELSE] END block
        // but it works
        // create a tempoary AST with only the nodes after the current IF block nodes
        // $newAst= array();
        // $i = 0;
        // // ignore  2 nodes with forvariable and fortemplate
        // foreach ($ast as $key => $value) {
        //     $i++;
        //     if ($i == 1) continue;
        //     if ($i == 2) continue;
            
        //     $newAst[$key] =$value;
        //     $i++;
        // }
        // echo "\n\n newAst=";
        // print_r($newAst);
        
        // self::traverseAST($newAst, $level, $data, $html);
    }

    private static function traverseAstIf($ast, $level, $data, &$html) {      
        if (!isset($ast['ifvariable'])) {
            \Logger::logDebugPrintR("'traversAstIf' [" . __LINE__ ."]    no ifvariable set :-((   ", $data); 
            \Util::quit500("Fatal Error - 'traversAstIf' [" . __LINE__ ."]    no ifvariable set :-((  ", $data);
        }
        $variable = strtolower($ast['ifvariable']);
        // \Logger::logDebug( "\nIF found variable = $variable\n");

        if (!isset($data[$variable])) {
            // \Logger::logDebug("\nIF  variable  $variable NOT found in 'data'");
            \Logger::logDebugPrintR("'traversAstIf' [" . __LINE__ ."]    variale $variable  NOT set in data   ", $data); 
            \Util::quit500("Fatal Error - 'traversAstIf' [" . __LINE__ ."]    variale $variable  NOT set in data  ", $data);
        }

        if (!isset($ast["IFTRUE"])) {
            \Logger::logDebugPrintR("'traversAstIf' [" . __LINE__ ."]    IFTRUE  NOT set in ast   ", $ast); 
            \Util::quit500("Fatal Error - 'traversAstIf' [" . __LINE__ ."]    IFTRUE  NOT set in ast  ", $ast);
        }

        if ($data[$variable]) {
            self::traverseAST($ast["IFTRUE"], $level+1, $data, $html);
            $html  .= "\n";
        } else if (isset($ast["IFFALSE"])) {
            // \Logger::logDebugPrintR("'traverseAstIf' [" . __LINE__ ."] calling IFFALSE part of ast  =  ", $ast); 
            self::traverseAST($ast["IFFALSE"], $level+1, $data, $html);
            $html  .= "\n";
        }  else {
            \Logger::logDebugPrintR("'traversAstIf' [" . __LINE__ ."]    IFFALSE  NOT set in ast   ", $ast); 
            \Util::quit500("Fatal Error - 'traversAstIf' [" . __LINE__ ."]    IFFALSE  NOT set in ast  ", $ast);
        }
        if (isset($ast["AFTERIF"])) {
            self::traverseAST($ast["AFTERIF"], $level, $data, $html, true );
        }


        // ohhh boy - that's an ugly hack. somehow there has to be a better way
        // same hack as in the traversesForEach function
        // $newAst= array();
        // $i = 0;
        // // max 3 nodes with ifvariable, IFTRUE, optional IFFALSE
        // foreach ($ast as $key => $value) {
        //     $i++;
        //     // echo "\n key = $key,  i = $i";
        //     if ($i == 1) continue;
        //     if ($i == 2) continue;
        //     if ($i == 3 && $key=="IFFALSE") continue;
        //     $newAst[$key] =$value;
        //     $i++;
        // }
        // // echo "\n\n newAst=";
        // // print_r($newAst);
        
        // self::traverseAST($newAst, $level, $data, $html);
    }

    private static function traverseAST($ast, $level, $data, &$html) {
        // \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."] ast  =  ", $ast); 
        foreach ($ast as $key => $node) {
            if ($key === "FOREACH") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) . " FOREACH\n");
                self::traverseAstForEach($node, $level+1, $data, $html);
            } else if ($key === "IF") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." IF\n");
                self::traverseAstIf($node, $level+1, $data, $html);
            } else if ($key === "IFTRUE") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." IFTRUE\n");
                \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."] IFTRUE found  this should be handled by  'traverseAstIf'  node =  ", $node); 
                \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] IFTRUE found this should be handled by  'traverseAstIf'  ", $node);
            } else if ($key === "IFFALSE") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." IFFALSE\n");
                \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."]  IFFALSE found   this should be handled by  'traverseAstIf'  node =  ", $node); 
                \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] IFFALSE  found  this should be handled by  'traverseAstIf'  ", $node);
            } else if ($key === "ifvariable") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." ifvariable\n");
                \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."]  'ifvariable' found   this should be handled by  'traverseAstIf'  node =  ", $node); 
                \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] 'ifvariable'  found  this should be handled by  'traverseAstIf'  ", $node);
            } else if ($key === "forvariable") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." forvariable\n");
                \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."]  'forvariable' found   this should be handled by  'traverseForeach'  node =  ", $node); 
                \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] 'forvariable'  found  this should be handled by  'traverseForeach'  ", $node);                
            } else if ($key === "ELSE") {
                // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." ELSE\n");
                \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."]  'ELSE' found   this should be handled by  'traverseAstIf'  node =  ", $node); 
                \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] 'ELSE'  found  this should be handled by  'traverseAstIf'  ", $node);
            // } // else if ($key === "ELSE") {
            //     echo "\n" . str_pad("", $level * 3) ." HTML\n";
            //     $html .= $node->txt . "\n"; 
            } else  {
                // HTMLCODE or VARIABLE
                // print_r($node);
                if ($node["name"] ==="HTMLCODE") {
                    // \Logger::logDebug( "\n" . str_pad("", $level * 3) ." HTMLCODE\n");
                    $html .= $node["text"];
                } else if ($node["name"] === "VARIABLE") {
                    $variablename = strtolower(  $node["text"]);
                    \Logger::logDebug( "\n" . str_pad("", $level * 3) ." VARIABLE   name = $variablename \n");

                    if (!isset($data[$variablename])) {
                        \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."]  variable '$variablename' not found in data   =  ", $data); 
                        \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."]  variable '$variablename' not found in data   ", $data);
                    }
                    $html .= $data[$variablename];

                } else {
                    \Logger::logDebugPrintR("'traverseAST' [" . __LINE__ ."]  well - we should never end up here :-(  node =  ", $node); 
                    \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] 'well - we should never end up here :-(  ", $node);                
                }
            }
        }
    }

    private static function findTemplateByName(string $tmpl, string $tmplname) : int  {
        // $searchString = "<!-- ###" . $tmplname . "###";
        return strpos($tmpl, $tmplname);
    }

    private static function getPartial(string $tmpl)  {
        $start = \ApplicationConfig::$PARTIALBEGIN;
        $end =  \ApplicationConfig::$PARTIALEND;

        // \Logger::logDebug("getPartial() [".  __LINE__  . "]    start  =  $start,   end = $end", "");

        $pattern = '/' . preg_quote($start) . '(.*?';
        $pattern .= ')' . preg_quote($end) . '/s';
        // \Util::my_var_dump( $pattern, "TemplateEngine::find_between()   pattern" );
        $i = preg_match($pattern, $tmpl, $matches);

        // \Logger::logDebugPrintR("getPartial() [".  __LINE__  . "]    matches", $matches);

        // $code = null;
        // if ($i) {
        //     $code = $matches[0];
        // }
        return $matches;
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

        return $string;
    }
   
    private static function renderTemplate(string $tmplFilename, $data) : string {
        $partial = file_get_contents ($tmplFilename);
        if (!$partial) {
            \Util::quit500("Fatal Error - TemplateEngine::render()   could not open file : " , $tmplFilename);
        }
        // \Logger::logDebug("renderTemplate() [".  __LINE__  . "]  partial = ", $partial);

        return self::renderTemplateString($partial, $data);
    } 

    private static function renderTemplateString(string $html, $data) : string {
        // var_dump($html);
        // $template = '';

        // \Logger::logDebug("renderTemplateString() [".  __LINE__  . "]  html = ", $html);

        $templateBegin = self::findTemplateByName($html,\ApplicationConfig::$TEMPLATEBEGIN);
        if ($templateBegin !== 0) {
            $templateEnd = self::findTemplateByName($html,\ApplicationConfig::$TEMPLATEEND);
            if ($templateEnd === 0) {
                \Util::quit500("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEEND . " not found in file " ,  $tmplFilename);
            }
            // well - there it is: subtract 2 and it works
            $template = substr($html, $templateBegin + strlen(\ApplicationConfig::$TEMPLATEBEGIN), $templateEnd - $templateBegin  - strlen(\ApplicationConfig::$TEMPLATEEND)-2);
        } else {
            \Logger::logDebug("renderTemplateString()  [".  __LINE__  . "]  could not find a  " . \ApplicationConfig::$TEMPLATEBEGIN  ." in the template = ", $template);
            \Util::quit500("Fatal Error - renderTemplateString()  [".  __LINE__  . "]  could not find a  " . \ApplicationConfig::$TEMPLATEBEGIN  ." in the template = ", $template);
        }
        // \Logger::logDebug("renderTemplateString()  [".  __LINE__  . "]  template  = ",$template);


        $partial = self::getPartial($template);
        // $i = 0; 
        while ($partial != null) {
            // \Logger::logDebug("renderTemplateString() [".  __LINE__  . "]   partial[0] with BEGIN_PARTIAL and END_PARTIAL     = ", $partial[0]);
            // \Logger::logDebug("renderTemplateString() [".  __LINE__  . "]   partial[1] WITHOUT  BEGIN_PARTIAL and END_PARTIAL     = ", $partial[1]);
            $tmplCode = self::find_between_all($partial[1], "<!-- ", " -->");
            // \Logger::logDebug("renderTemplateString() [".  __LINE__  . "] the  template code for the parser  = ", $tmplCode);
           
            try {
                $lexer = new TemplateLexer($tmplCode);
                $parser = new TemplateParser($lexer);
                $ast = $parser->parseTemplate();
            } catch (Exception $e) {
                \Logger::logDebugPrintR("renderTemplateString() [".  __LINE__  . "] exception found     = ", $e->getMessage());
                \Util::quit500("Fatal Error - TemplateEngine::render()   could not open file : " , $tmplFilename);
            }
            
            // \Logger::logDebugPrintR("renderTemplateString() [".  __LINE__  . "] the AST    = ", $ast);
            // \Logger::logDebugPrintR("renderTemplateString() [".  __LINE__  . "] the data    = ", $data);
            
            $renderedPartial = '';
            $level = 1;

            self::traverseAST($ast, $level, $data, $renderedPartial);

            // \Logger::logDebug("renderTemplateString() [".  __LINE__  . "] the rendered html for the partial   = ", $renderedPartial);

            // replace the code in the template with the rendere html 

            $template = str_replace($partial[0], $renderedPartial, $template);

            // \Logger::logDebug("renderTemplateString() [".  __LINE__  . "] the template with the partial replaced by the rendered html    = ", $template);

            // get next partial 
            $partial = self::getPartial($template);


            // // for debugging this makes sense :-)
            // $i++;
            // if ($i > 10) { die(); };
        }
        // \Logger::logDebug("renderTemplateString() [".  __LINE__  . "] the rendered html for the whole template   = ", $template);
        return $template;
    } 

    // template names can be null -> no string type  
    public static function render(string $tmplFilename, $data,  $headertemplate,  $contenttemplate,  $footertemplate) : string {
        // \Logger::logDebug("render() [".  __LINE__  . "] headertemplate    = ", $headertemplate);
        // \Logger::logDebug("render() [".  __LINE__  . "] contenttemplate    = ", $contenttemplate);
        // \Logger::logDebug("render() [".  __LINE__  . "] footertemplate    = ", $footertemplate);
        // \Logger::logDebug("render() [".  __LINE__  . "] tmplFilename    = ", $tmplFilename);

        $template = file_get_contents ($tmplFilename);
        if (!$template) {
            \Logger::logDebug("TemplateEngine::render() [".  __LINE__  . "]  could not open file = $tmplFilename", "");
            \Util::quit500("Fatal Error - TemplateEngine::render()   could not open file : " , $tmplFilename);
        }

        \Logger::logDebug("render() [".  __LINE__  . "] template    = ", $template);
        // \Logger::logDebug("render() [".  __LINE__  . "] headertemplate    = ", $headertemplate);


        // check if there is a header, footer or content template
        if ($headertemplate !== null) {
            $headerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEHEADER);
            // \Logger::logDebug("render() [".  __LINE__  . "] headerBegin = $headerBegin   = ", "");

            if  ($headerBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_HEADER###'";
                // echo "<br><br> template BEFORE replacing = " . htmlspecialchars($template) . "<br><br>";
                $tmp = self::renderTemplate($headertemplate, $data);
                // \Logger::logDebug("render() [".  __LINE__  . "] headertemplate  after renderTemplate()   = ", $tmp);
                $template = str_replace(\ApplicationConfig::$TEMPLATEHEADER, self::renderTemplate($headertemplate, $data), $template);
                // echo "<br><br> template AFTER replacing = " . htmlspecialchars($template) . "<br><br>";
            }
        }
        // \Logger::logDebug("render() [".  __LINE__  . "] template after header is inserted    = ", $template);
 
        if ($contenttemplate !== null) {
            $contentBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATECONTENT);
            if  ($contentBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_CONTENT###'";
                $template = str_replace(\ApplicationConfig::$TEMPLATECONTENT, self::renderTemplate($contenttemplate, $data), $template);
            }
        }

        if ($footertemplate !== null) {
            $footerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEFOOTER);
            if  ($footerBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_FOOTER###'";
                $template = str_replace(\ApplicationConfig::$TEMPLATEFOOTER, self::renderTemplate($footertemplate, $data), $template);

            }
        }
        
        return $template;
    }
}
