<?php

class TemplateEngine {

    private static function findTemplate(string $tmpl)  {
        $posBegin = strpos($tmpl, "###");
        $ret["found"] = false; 
        if ($posBegin > 0) {
            // is it a <!-- ### or just a simple ###  ??? >
            if (substr($tmpl, $posBegin - 5) == "<!-- ") {
                $posBegin = strpos($tmpl, "<!-- ###");
                $tmp = $posBegin + strlen("<!-- ###") ;
                $posEnd = strpos($tmpl, "-->", $tmp) +3;
                $ret["begin"] = $posBegin; 
                $ret["end"] = $posEnd; 
                $ret["found"] = true; 
                $ret["tmplname"] = trim(substr($tmpl, $tmp, $posEnd - $tmp));
            } else {
                $tmp = $posBegin + strlen("###") ;
                $posEnd = strpos($tmpl, "###", $tmp);
                $ret["begin"] = $posBegin; 
                $ret["end"] = $posEnd; 
                $ret["found"] = true; 
                $ret["tmplname"] = trim(substr($tmpl, $tmp, $posEnd - $tmp));
            }
        }
        return $ret;
    }

    // private static function findTemplateByName(string $tmpl, string $tmplname) : int  {
    //     $searchString = "<!-- ###" . $tmplname . "###";
    //     return  strpos($tmpl, $searchString);
    // }

    private static function findTemplateByName(string $tmpl, string $tmplname) : int  {
        return strpos($tmpl, $tmplname);
    }

    private static function replaceVariable(string $html, $data, $variable) : string   {
        if (!isset($data[$variable])) {
            // // \Util::my_var_dump( $data, "TemplateEngine::replaceVariable()   could not find key " . $variable . " in object data  ");
            \Logger::logError("TemplateEngine::replaceVariable()   could not find key " . $variable . " in object data " , "");
            // readfile('static/500.html');
            exit();
        }
        // echo "<br> in replaceVariable!!! yeah baby! <br/>";
        // // \Util::my_var_dump( $data, "replaceVariable()   data = ");
        // // \Util::my_var_dump( htmlspecialchars($html) , "replaceVariable()   html = ");
        // // \Util::my_var_dump( $variable , "replaceVariable()   variable = ");

        $html = str_replace("###" . $variable . "###", $data[$variable], $html);
        // // \Util::my_var_dump( htmlspecialchars($html) , "replaceVariable() new with replaced text  html = ");

        return $html;
    }
    

    private static function renderIf(string $template, $data, $variable): string {
        $objKey = strtolower($variable);
        if (!isset($data[$objKey])) {
            // // \Util::my_var_dump( $data, "TemplateEngine::replaceVariable()   could not find key " . $variable . " in object data  ");
            \Logger::logError("TemplateEngine::renderIf()   could not find key " . $variable . " in object data " , "");
            // readfile('static/500.html');
            exit();
        }

        $template = trim($template);
        echo "<br> <br> IF  ...  html code = " . htmlspecialchars($template) ."<br><br>";
        // // \Util::my_var_dump( $variable, "renderForEach()   variable = ");
        // // \Util::my_var_dump( $data, "renderForEach()   data = ");
        // // \Util::my_var_dump( htmlspecialchars($template), "renderForEach()  template = ");
        $html = '';

        $elseTag = "<!-- ###" . \ApplicationConfig::$TEMPLATEIFELSE . "### " . $variable ." -->";
        $posElse = strpos($template,  $elseTag);
        if ($posElse == 0) {
            \Util::my_var_dump( $data, "TemplateEngine::renderIf()   could not find IF for variable  '" . $variable . "' in html template  ");
            \Logger::logError("TemplateEngine::renderIf()   could not find IF for variable  '" . $variable . "' in html template" , "");
            // readfile('static/500.html');
            exit();
        }
        $startTag = "<!-- ###" . \ApplicationConfig::$TEMPLATEIF . "### " . $variable ." -->";
        $endTag = "<!-- ###" . \ApplicationConfig::$TEMPLATEIFEND . "### " . $variable ." -->";

        $posStartTag = strpos($template, $startTag) + strlen($startTag);
        $posEndTag = strpos($template, $endTag);

        $trueHtmlTmp = trim(substr($template, $posStartTag, $posElse - $posStartTag));

        \Util::my_var_dump( htmlspecialchars($startTag), "TemplateEngine::renderIf()   startTag  ");
        \Util::my_var_dump( htmlspecialchars($elseTag), "TemplateEngine::renderIf()   elseTag  ");
        \Util::my_var_dump( htmlspecialchars($endTag), "TemplateEngine::renderIf()   endTag  ");


        \Util::my_var_dump( $posStartTag, "TemplateEngine::renderIf()   posStartTag  ");
        \Util::my_var_dump( $posElse, "TemplateEngine::renderIf()   posElse  ");
        \Util::my_var_dump( $posEndTag, "TemplateEngine::renderIf()   posEndTag  ");

        $falseHtmlTmp = trim(substr($template, $posElse +strlen($elseTag), $posEndTag - $posElse -strlen($elseTag)));

        \Util::my_var_dump( htmlspecialchars($trueHtmlTmp), "TemplateEngine::renderIf()   trueHtmlTmp  ");

        \Util::my_var_dump( htmlspecialchars($falseHtmlTmp), "TemplateEngine::renderIf()   falseHtmlTmp  ");


        // TRUE HTML
        // only the code in the comment is relevant ...
        $idxBeginComment = strpos($trueHtmlTmp, "<!--T") ;
        $idxEndComment = strpos($trueHtmlTmp, "T-->");

        if (($idxBeginComment < 0) || ($idxEndComment < 0 )) {
            \Util::my_var_dump( $data, "TemplateEngine::renderIf()   could not find template code in comments for TRUE path  '" . $variable . "' in html template  ");
            \Logger::logError("TemplateEngine::renderIf()   could not find template code in comments for TRUE path   '" . $variable . "' in html template" , "");
            // readfile('static/500.html');
            exit();
        }
        $idxBeginComment = $idxBeginComment + strlen("<!--T ");
        $trueHtml = trim(substr($trueHtmlTmp, $idxBeginComment, $idxEndComment - $idxBeginComment));

        \Util::my_var_dump( htmlspecialchars($trueHtml), "TemplateEngine::renderIf()   trueHtml  ");

        // FALSE HTML
        // only the code in the comment is relevant ...
        $idxBeginComment = strpos($falseHtmlTmp, "<!--T") ;
        $idxEndComment = strpos($falseHtmlTmp, "T-->");

        if (($idxBeginComment < 0) || ($idxEndComment < 0 )) {
            \Util::my_var_dump( $data, "TemplateEngine::renderIf()   could not find template code in comments for FALSE path  '" . $variable . "' in html template  ");
            \Logger::logError("TemplateEngine::renderIf()   could not find template code in comments for FALSE path  '" . $variable . "' in html template" , "");
            // readfile('static/500.html');
            exit();
        }
        $idxBeginComment = $idxBeginComment + strlen("<!--T ");
        $falseHtml = trim(substr($falseHtmlTmp, $idxBeginComment, $idxEndComment - $idxBeginComment));

        \Util::my_var_dump( htmlspecialchars($falseHtml), "TemplateEngine::renderIf()   falseHtml  ");

        if ($data[$objKey]) {
            $html = self::renderPartialString($trueHtml, $data) . "\n";
            // // \Util::my_var_dump( htmlspecialchars($html) , "for loop new html  = ");
        } else {
            $html = self::renderPartialString($falseHtml, $data) . "\n";
        }
        return $html;
    }

    private static function renderForEach(string $template, $data, $variable): string {
       
        // echo "<br> <br> for loop html code = " . htmlspecialchars($template) ."<br><br>";
        $objKey = strtolower($variable);
        // // \Util::my_var_dump( $variable, "renderForEach()   variable = ");
        // // \Util::my_var_dump( $data, "renderForEach()   data = ");
        // // \Util::my_var_dump( htmlspecialchars($template), "renderForEach()  template = ");
        $html = '';

        if (!isset($data[$objKey])) {
            // \Util::my_var_dump( $data, "TemplateEngine::renderForEach()   could not find key " . $objKey . " in object data   = ");
            \Logger::logError("TemplateEngine::renderForEach()   could not find key " . $objKey . " in object data " , "");
            // readfile('static/500.html');
            exit();
        }

        // remove the plural s from the variable
        $var = trim(substr($variable, 0, strlen($variable)-1));
        foreach ($data[$objKey] as $val ) {
            // // \Util::my_var_dump( $val, "for loop  val  = ");
            // // \Util::my_var_dump( htmlspecialchars($template), "template  = ");
            $html = $html . self::renderPartialString($template, $val) . "\n";
            // // \Util::my_var_dump( htmlspecialchars($html) , "for loop new html  = ");
        }
        return $html;
    }

    private static function renderPartial(string $tmplFilename, $data) : string {
        // echo "<br> 'renderPartial with tmplFilename = " . $tmplFilename ."<br/>"; 
        $partial = file_get_contents ($tmplFilename);
        if (!$partial) {
            \Logger::logError("TemplateEngine::render()   could not open file : " , $tmplFilename);
            readfile('static/500.html');
            exit();
        }
        return self::renderPartialString($partial, $data);
    } 

    private static function renderPartialString(string $partial, $data) : string {
        $templateBegin = self::findTemplateByName($partial,\ApplicationConfig::$TEMPLATEBEGIN);
        if ($templateBegin !== 0) {
            $templateEnd = self::findTemplateByName($partial,\ApplicationConfig::$TEMPLATEEND);
            if ($templateEnd === 0) {
                \Logger::logError("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEEND . " not found in file " ,  $tmplFilename);
                readfile('static/500.html');
                exit();
            }
            // echo "<br>  found a TEMPLATEBEGIN keyowrd at pos  " . $templateBegin . "<br>";
            // echo "<br>  found a TEMPLATEENDkeyowrd at pos  " . $templateEnd . "<br>";

            // well - there it is: subtract 2 and it works
            $partial =  substr($partial, $templateBegin + strlen(\ApplicationConfig::$TEMPLATEBEGIN), $templateEnd - $templateBegin  - strlen(\ApplicationConfig::$TEMPLATEEND)-2);
        } 
        // // \Util::my_var_dump( htmlspecialchars($partial), "renderPartialString()    partial  = ");


        
        // find next ###
        $nextTemplate = self::findTemplate($partial);
        // // \Util::my_var_dump( $nextTemplate, "nextTemplate  = ");

        while ($nextTemplate["found"]) {
            switch ($nextTemplate["tmplname"]) {
                case \ApplicationConfig::$TEMPLATEFOREACHBEGIN:

                    // echo "<br><br>xxxxxxxxxxxxxxxxxxxxxxx<br>found for each<br>xxxxxxxxxxxxxxxxxxxxxxx<br><br><br>";

                    $foreachEnd = self::findTemplateByName($partial, \ApplicationConfig::$TEMPLATEFOREACHEND);
                    if ($foreachEnd === FALSE) {
                        \Logger::logError("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEFOREACHEND . " not found in file " ,  $tmplFilename);
                        readfile('static/500.html');
                        exit();
                    }

                    // BIG TODO: remove this BS and use regexes


                    $tmp = trim(substr($partial, $nextTemplate["begin"], $nextTemplate["end"] - $nextTemplate["begin"]));
                    // // \Util::my_var_dump(htmlspecialchars( $tmp), "tmp  = ");

                    // read variable name 
                    $tmp = "<!-- ###" . \ApplicationConfig::$TEMPLATEFOREACHBEGIN . "### ";
                    $idxEndForEach = strpos($partial, $tmp ) +strlen($tmp);
                    $endComment = strpos($partial, "-->", $idxEndForEach);
                    // // \Util::my_var_dump(htmlspecialchars( $tmp), "tmp  = ");

                    // // \Util::my_var_dump(strlen($idxEndForEach), "idxEndForEach  = ");
                    // // \Util::my_var_dump(strlen($endComment), "endComment  = ");

                    $variable = trim(substr($partial, $idxEndForEach, $endComment - $idxEndForEach));
                    // // \Util::my_var_dump(strlen($variable), "strlen(variable)  = ");

                    // \Util::my_var_dump(htmlspecialchars( $variable), "variable  = ");

                    $tmp = $tmp . $variable . " -->";

                    // // \Util::my_var_dump(htmlspecialchars( $tmp), "full starting tag tmp  = ");
                    $posBeginFor = $nextTemplate["begin"] + strlen($tmp) ;


                    $endTagForEach = "<!-- ###" . \ApplicationConfig::$TEMPLATEFOREACHEND . "### " . $variable ." -->";
                    // // \Util::my_var_dump(htmlspecialchars( $endTagForEach), "full endTagForEach  = ");
                    $idxEnd = strpos($partial, $endTagForEach);
                    $forloopTmp = trim(substr($partial, $posBeginFor , $idxEnd - $posBeginFor));
                    // // \Util::my_var_dump(htmlspecialchars($forloopTmp), "html code forloopTmp  = ");
                    
                    // forLoopTmp contains the foor loop in <!-- --> and the html code -> cut out the template code
                    $beginTag = "<!--T";
                    $endTag = "T-->";
                    $startIndex = strpos($forloopTmp, $beginTag) + strlen($beginTag);
                    $endIndex = strpos($forloopTmp, $endTag) ;

                    $forLoopHtml = trim(substr($forloopTmp, $startIndex, $endIndex - $startIndex));
                    
                    // \Util::my_var_dump(htmlspecialchars($forLoopHtml), "renderPartialString()   forLoopHtml  = ");
                    // // \Util::my_var_dump($data, "renderPartialString ()  data  = ");
                    // \Util::my_var_dump($variable, "renderPartialString ()  variable  = ");

                    $htmlForLoop = self::renderForEach($forLoopHtml, $data, $variable);
                    // renderForEach(string $template, $data, $variable)

                    // // TODO: this is not the best way? replace the $$forloop with some ???

                    $startTag  = "<!-- ###" . \ApplicationConfig::$TEMPLATEFOREACHBEGIN . "### " . $variable ." -->";
                    $endTag  = "<!-- ###" . \ApplicationConfig::$TEMPLATEFOREACHEND . "### " . $variable ." -->";

                    $startIdx = strpos($partial, $startTag);
                    $endIdx = strpos($partial, $endTag, $startIdx) + strlen($endTag);

                    // \Util::my_var_dump(htmlspecialchars($startTag), "renderPartialString()  startTag  = ");
                    // \Util::my_var_dump(htmlspecialchars($endTag), "renderPartialString()  endTag  = ");
                    // \Util::my_var_dump($startIdx, "renderPartialString()  startIdx  = ");
                    // // \Util::my_var_dump($endIdx, "renderPartialString()  endIdx  = ");


                    $strToBeReplaced = substr($partial, $startIdx, $endIdx - $startIdx);
                    // \Util::my_var_dump(htmlspecialchars($htmlForLoop), "renderPartialString()  this is the resulting html code  = ");
                    // // \Util::my_var_dump(htmlspecialchars($strToBeReplaced), "renderPartialString()  this is the code we want to replace  strToBeReplaced   = ");


                    $partial = str_replace($strToBeReplaced, $htmlForLoop, $partial);
                    break;

                case \ApplicationConfig::$TEMPLATEIF:
                    echo "<br><br>xxxxxxxxxxxxxxxxxxxxxxx<br>found IF<br>xxxxxxxxxxxxxxxxxxxxxxx<br><br><br>";
                    
                    // read the variable name from the template
                    $tmp = trim(substr($partial, $nextTemplate["begin"], $nextTemplate["end"] - $nextTemplate["begin"]));
                    // // \Util::my_var_dump(htmlspecialchars( $tmp), "tmp  = ");

                    // read variable name 
                    $tmp = "<!-- ###" . \ApplicationConfig::$TEMPLATEIF . "### ";
                    $idxEndForEach = strpos($partial, $tmp ) +strlen($tmp);
                    $endComment = strpos($partial, "-->", $idxEndForEach);
                    // // \Util::my_var_dump(htmlspecialchars( $tmp), "tmp  = ");

                    // // \Util::my_var_dump(strlen($idxEndForEach), "idxEndForEach  = ");
                    // // \Util::my_var_dump(strlen($endComment), "endComment  = ");

                    $variable = trim(substr($partial, $idxEndForEach, $endComment - $idxEndForEach));

                    \Util::my_var_dump($variable, "IF   variable  = ");
                    $startTag  = "<!-- ###" . \ApplicationConfig::$TEMPLATEIF . "### " . $variable ." -->";
                    $endTag  = "<!-- ###" . \ApplicationConfig::$TEMPLATEIFEND . "### " . $variable ." -->";

                    $startIdx = strpos($partial, $startTag);
                    $endIdx = strpos($partial, $endTag, $startIdx) + strlen($endTag);

                    \Util::my_var_dump($startIdx, "IF   startIdx  = ");
                    \Util::my_var_dump($endIdx, "IF   endIdx  = ");

                    $strToBeReplaced = substr($partial, $startIdx, $endIdx - $startIdx);
                    \Util::my_var_dump(htmlspecialchars($strToBeReplaced), "renderPartialString() IF  this is the code we want to replace  strToBeReplaced   = ");

                    $htmlIf = self::renderIf($strToBeReplaced, $data, $variable);

                    \Util::my_var_dump(htmlspecialchars($htmlIf), "renderPartialString() IF  this is the new code    htmlIf   = ");

                    $partial = str_replace($strToBeReplaced, $htmlIf, $partial);
                    break;
                
                default:
                    // echo "<br><br>xxxxxxxxxxxxxxxxxxxxxxx<br>found a variable which should be subsituted<br>xxxxxxxxxxxxxxxxxxxxxxx<br><br><br>";
                    $partial = self::replaceVariable($partial, $data, $nextTemplate["tmplname"]);
                    break;
            }
            $nextTemplate = self::findTemplate($partial);
        }
        // // \Util::my_var_dump($nextTemplate, "next template = ");
        // echo "<br><br> html = " . htmlspecialchars($html) . "<br><br>";

        return $partial;
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
        if ($headertemplate !== null) {
            $headerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEHEADER);
            if  ($headerBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_HEADER###'";
                // echo "<br><br> template BEFORE replacing = " . htmlspecialchars($template) . "<br><br>";
                $template = str_replace(\ApplicationConfig::$TEMPLATEHEADER, self::renderPartial($headertemplate, $data), $template);
                // echo "<br><br> template AFTER replacing = " . htmlspecialchars($template) . "<br><br>";
            }
        }
        
        if ($contenttemplate !== null) {
            $contentBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATECONTENT);
            if  ($contentBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_CONTENT###'";
                $template = str_replace(\ApplicationConfig::$TEMPLATECONTENT, self::renderPartial($contenttemplate, $data), $template);
            }
        }

        if ($footertemplate !== null) {
            $footerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEFOOTER);
            if  ($footerBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_FOOTER###'";
                $template = str_replace(\ApplicationConfig::$TEMPLATEFOOTER, self::renderPartial($footertemplate, $data), $template);

            }
        }
        // file_put_contents('tmp.html', $template);
        return $template;
    }
}
