<?php

class TemplateEngine {

    private $filename = null; 

    private static function findTemplate(string $tmpl)  {
        $posBegin = strpos($tmpl, "<!-- ###");
        $ret["found"] = false; 
        if ($posBegin > 0) {
            $tmp = $posBegin + strlen("<!-- ###") ;
            $posEnd = strpos($tmpl, "-->", $tmp) +3;
            $ret["begin"] = $posBegin; 
            $ret["end"] = $posEnd; 
            $ret["found"] = true; 
            $ret["tmplname"] = substr($tmpl, $tmp, $posEnd - $tmp);
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

    private static function replaceVariable(string $html, $data) : string   {
        /// TODO replace varibales in $html with values from corresponding keys
        

        return $html; 
    }

    private static function renderForEach(string $template, $data, $variable): string {
       
        // echo "<br> <br> for lopp html code = " . htmlspecialchars($template) ."<br><br>";
        $objKey = strtolower($variable);

        $html = '';

        if (!isset($data[$objKey])) {
            \Logger::logError("TemplateEngine::renderForEach()   could not find key " . $objKey . " in object data " , $data);
            readfile('static/500.html');
            exit();
        }

        // remove the plural s from the variable
        $var = substr($variable, 0, strlen($variable)-1);
        foreach ($data[$objKey] as $val ) {
            // \Util::my_var_dump( $val, "val  = ");
            $html = $html . self::renderPartialString($template, $val);
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
        if ($templateBegin !== FALSE) {
            $templateEnd = self::findTemplateByName($partial,\ApplicationConfig::$TEMPLATEEND);
            if ($templateEnd === FALSE) {
                \Logger::logError("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEEND . " not found in file " ,  $tmplFilename);
                readfile('static/500.html');
                exit();
            }
        }
            // echo "<br>  templateBegin = " .$templateBegin . "<br>";
            // echo "<br>  templateEnd = " .$templateEnd . "<br>";

            // nobody knows why -3 is necessary, but it is ...
        $html = substr($partial, $templateBegin + strlen(\ApplicationConfig::$TEMPLATEBEGIN), $templateEnd - $templateBegin  - strlen(\ApplicationConfig::$TEMPLATEEND) - 3);
        // \Util::my_var_dump( htmlspecialchars($html), "html  = ");


        // find next <!-- ###
        $nextTemplate = self::findTemplate($html);
        // \Util::my_var_dump( $nextTemplate, "nextTemplate  = ");

        if ($nextTemplate["found"]) {
            switch ($nextTemplate["found"]){
                case \ApplicationConfig::$TEMPLATEFOREACHBEGIN:
                    $foreachEnd = self::findTemplateByName($html, \ApplicationConfig::$TEMPLATEFOREACHEND);
                    if ($foreachEnd === FALSE) {
                        \Logger::logError("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEFOREACHEND . " not found in file " ,  $tmplFilename);
                        readfile('static/500.html');
                        exit();
                    }

                    // BIG TODO: remove this BS and use regexes
                    $tmp = substr($html, $nextTemplate["begin"], $nextTemplate["end"] - $nextTemplate["begin"]);
                    // \Util::my_var_dump(htmlspecialchars( $tmp), "tmp  = ");

                    // read variable name 
                    $tmp = "<!-- ###" . \ApplicationConfig::$TEMPLATEFOREACHBEGIN . "### ";
                    $idxEndForEach = strpos($html, $tmp ) +strlen($tmp);
                    $endComment = strpos($html, "-->", $idxEndForEach);
                    // \Util::my_var_dump(htmlspecialchars( $tmp), "tmp  = ");

                    // \Util::my_var_dump(strlen($idxEndForEach), "idxEndForEach  = ");
                    // \Util::my_var_dump(strlen($endComment), "endComment  = ");

                    $variable = trim(substr($html, $idxEndForEach, $endComment - $idxEndForEach));
                    // \Util::my_var_dump(strlen($variable), "strlen(variable)  = ");

                    // \Util::my_var_dump(htmlspecialchars( $variable), "variable  = ");

                    $tmp = $tmp . $variable . " -->";

                    // \Util::my_var_dump(htmlspecialchars( $tmp), "full starting tag tmp  = ");
                    $posBeginFor = $nextTemplate["begin"] + strlen($tmp) ;


                    $endTagForEach = "<!-- ###" . \ApplicationConfig::$TEMPLATEFOREACHEND . "### " . $variable ." -->";
                    // \Util::my_var_dump(htmlspecialchars( $endTagForEach), "full endTagForEach  = ");
                    $idxEnd = strpos($html, $endTagForEach);
                    $forloopTmp = trim(substr($html, $posBeginFor , $idxEnd - $posBeginFor));
                    // \Util::my_var_dump(htmlspecialchars($forloopTmp), "html code forloopTmp  = ");
                    
                    // forLoopTmp contains the foor loop in <!-- --> and the html code -> cut out the template code
                    $beginTag = "<!--";
                    $endTag = "-->";
                    $startIndex = strpos($forloopTmp, $beginTag) + strlen($beginTag);
                    $endIndex = strpos($forloopTmp, $endTag) ;

                    $forLoopHtml = substr($forloopTmp, $startIndex, $endIndex - $startIndex);
                    // \Util::my_var_dump(htmlspecialchars($forLoopHtml), "forLoopHtml  = ");
                    
                    $htmlForLoop = self::renderForEach($forLoopHtml, $data, $variable);


                    // // TODO: this is not the best way? replace the $$forloop with some ???
                    // $html = str_replace($forloop, $htmlForLoop, $html);
                    break;

                case \ApplicationConfig::$TEMPLATEIF:
                    // echo "i ist gleich 1";
                    break;
                
                default:
                    $html = self::replaceVariable($html, $data);
                    break;
            }
        }
        // \Util::my_var_dump($nextTemplate, "next template = ");
        // /echo "<br><br> html = " . htmlspecialchars($html) . "<br><br>";

        return $html;
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
