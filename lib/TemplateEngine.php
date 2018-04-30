<?php

class TemplateEngine {

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
    
    private static function findTemplateByName(string $tmpl, string $tmplname) : int  {
        return strpos($tmpl, $tmplname);
    }

    private static function replaceVariable(string $html, $data, $variable) : string   {
        if (!isset($data[$variable])) {
            \Util::my_var_dump( $data, "TemplateEngine::replaceVariable()   could not find key " . $variable . " in object data  ");
            \Logger::logError("TemplateEngine::replaceVariable()   could not find key " . $variable . " in object data " , "");
            // readfile('static/500.html');
            exit();
        }
        echo "<br> in replaceVariable!!! yeah baby! <br/>";
        \Util::my_var_dump( $data, "replaceVariable()   data = ");
        \Util::mys_var_dump( htmlspecialchars($html) , "replaceVariable()   html = ");
        \Util::my_var_dump( $variable , "replaceVariable()   variable = ");

        $html = str_replace("###" . $variable . "###", $data[$variable], $html);
        // \Util::my_var_dump( htmlspecialchars($html) , "replaceVariable() new with replaced text  html = ");

        return $html;
    }
    
    private static function renderPartial(string $tmplFilename, $data) : string {
        $partial = file_get_contents ($tmplFilename);
        if (!$partial) {
            \Logger::logError("TemplateEngine::render()   could not open file : " , $tmplFilename);
            readfile('static/500.html');
            exit();
        }
        return self::renderPartialString($partial, $data);
    } 

    private static function renderPartialString(string $partial, $data) : string {
        // echo "<br/><br/>renderPartialString12 () <br/> <br/>";
        // \Util::my_var_dump( htmlspecialchars($partial), "renderPartialString()    partial  = ");
        $templateBegin = self::findTemplateByName($partial,\ApplicationConfig::$TEMPLATEBEGIN);
        if ($templateBegin !== 0) {
            $templateEnd = self::findTemplateByName($partial,\ApplicationConfig::$TEMPLATEEND);
            if ($templateEnd === 0) {
                \Logger::logError("Fatal Error - closing " . \ApplicationConfig::$TEMPLATEEND . " not found in file " ,  $tmplFilename);
                readfile('static/500.html');
                exit();
            }
            // well - there it is: subtract 2 and it works
            $partial =  substr($partial, $templateBegin + strlen(\ApplicationConfig::$TEMPLATEBEGIN), $templateEnd - $templateBegin  - strlen(\ApplicationConfig::$TEMPLATEEND)-2);
        } 
        \Util::my_var_dump( htmlspecialchars($partial), "renderPartialString()    partial  = ");

        $templateCode = self::getTemplateCode($partial);
        \Util::my_var_dump( htmlspecialchars($templateCode), "renderPartialString()    templateCode from partial   = ");

        if ($templateCode != null) {
            echo "<br/><br/><br/>";
            \Util::my_var_dump( htmlspecialchars($templateCode), "renderPartialString()    templateCode from partial   = ");
            echo "<br/><br/><br/>";
        
            while ($templateCode!== null) {
                echo "<br/><br/><br/>";
                \Util::my_var_dump( htmlspecialchars($templateCode), "renderPartialString()   replacing tthis with 'REPLACED'   = ");
                echo "<br/><br/><br/>";

                
                $tmp = self::find_between_all($templateCode, "<!-- ", " -->");
                echo "<br><br><br>";
                \Util::my_var_dump( htmlspecialchars($tmp), "renderPartialString()   only code in comments   = ");
                echo "<br><br><br>";


                $lexer = new TemplateLexer($tmp);
                $parser = new TemplateParser($lexer);
                $res = $parser->parseTemplate();

                \Util::my_var_dump( $res, "renderPartialString()   partial res   = ");


                
                
                $partial = str_replace($templateCode, "REPLACED", $partial);
    
                echo "<br/><br/><br/>";
                // \Util::my_var_dump( htmlspecialchars($partial), "renderPartialString()   partial now   = ");
                echo "<br/><br/><br/>";
   
                $templateCode = self::getTemplateCode($partial);

                // echo "<br/><br/><br/>";
                // \Util::my_var_dump( htmlspecialchars($templateCode), "renderPartialString()   templateCode now   = ");
                // echo "<br/><br/><br/>";

            }
        }
       
        echo "<br/><br/><br/>";
                \Util::my_var_dump( htmlspecialchars($partial), "renderPartialString()   final partial returning   = ");
                echo "<br/><br/><br/>";

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
        // if ($headertemplate !== null) {
        //     $headerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEHEADER);
        //     if  ($headerBegin !== FALSE) {
        //         // echo "<br> found a '###TEMPLATE_HEADER###'";
        //         // echo "<br><br> template BEFORE replacing = " . htmlspecialchars($template) . "<br><br>";
        //         $template = str_replace(\ApplicationConfig::$TEMPLATEHEADER, self::renderPartial($headertemplate, $data), $template);
        //         // echo "<br><br> template AFTER replacing = " . htmlspecialchars($template) . "<br><br>";
        //     }
        // }
        
        if ($contenttemplate !== null) {
            $contentBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATECONTENT);
            if  ($contentBegin !== FALSE) {
                // echo "<br> found a '###TEMPLATE_CONTENT###'";
                $template = str_replace(\ApplicationConfig::$TEMPLATECONTENT, self::renderPartial($contenttemplate, $data), $template);
            }
        }

        // if ($footertemplate !== null) {
        //     $footerBegin = self::findTemplateByName($template, \ApplicationConfig::$TEMPLATEFOOTER);
        //     if  ($footerBegin !== FALSE) {
        //         // echo "<br> found a '###TEMPLATE_FOOTER###'";
        //         $template = str_replace(\ApplicationConfig::$TEMPLATEFOOTER, self::renderPartial($footertemplate, $data), $template);

        //     }
        // }
        // file_put_contents('tmp.html', $template);
        return $template;
    }
}
