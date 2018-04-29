<?php

class TemplateEngine {

    private $filename = null; 

    // private function findTemplate(string $tmpl)  {
    //     $posBegin = strpos($tmpl, "<!-- ###");
    //     $ret["found"] = false; 
    //     if ($pos > 0) {
    //         $tmp = $posBegin + strlen("<!-- ###") + 1;
    //         $posEnd = strpos($tmpl, "###", $tmp);
    //         $ret["found"] = $posBegin; 
    //         $ret["end"] = $posEnd; 
    //         $ret["tmplname"] = substr($tmpl, $tmp, $posEnd - $tmp + 1);
    //     }
    //     return $ret;
    // }

    // private static function findTemplateByName(string $tmpl, string $tmplname) : int  {
    //     $searchString = "<!-- ###" . $tmplname . "###";
    //     return  strpos($tmpl, $searchString);
    // }

    private static function findTemplateByName(string $tmpl, string $tmplname) : int  {
        return strpos($tmpl, $tmplname);
    }

    private static function renderPartial(string $tmplFilename, $data) : string {
        // echo "<br> 'renderPartial with tmplFilename = " . $tmplFilename ."<br/>"; 
        $partial = file_get_contents ( $tmplFilename);
        if (!$partial) {
            \Logger::logError("TemplateEngine::render()   could not open file : " , $tmplFilename);
            readfile('static/500.html');
            exit();
        }

        $templateBegin = self::findTemplateByName($partial,\ApplicationConfig::$TEMPLATEBEGIN);
        if ($templateBegin !== FALSE) {
            $templateEnd = self::findTemplateByName($partial,\ApplicationConfig::$TEMPLATEEND);
            if ($templateEnd === FALSE) {
                \Logger::logError("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEEND . " not found in file " ,  $tmplFilename);
                readfile('static/500.html');
                exit();
            }
            // echo "<br>  templateBegin = " .$templateBegin . "<br>";
            // echo "<br>  templateEnd = " .$templateEnd . "<br>";

            // nobody knows why -3
            $html = substr($partial, $templateBegin + strlen(\ApplicationConfig::$TEMPLATEBEGIN), $templateEnd - $templateBegin  - strlen(\ApplicationConfig::$TEMPLATEEND) - 3);
        }

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
                //echo "<br> found a '###TEMPLATE_HEADER###'";
    
                $template = str_replace(\ApplicationConfig::$TEMPLATEHEADER, self::renderPartial($headertemplate, $data), $template);
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
