<?php

class ApplicationConfig {

    public static $databaseName = 'fh_2018_scm4_S1610307036';
    public static $databaseUsername = 'fh_2018_scm4';
    public static $databasePassword = 'fh_2018_scm4';
    public static $databaseHost = 'localhost';

    public static $logFile = 'log/poormansslack.log';

    public static $indexTmpl = 'index.tmpl';

    
    public static $TEMPLATEHEADER = "<!-- ###TEMPLATE_HEADER### -->";
    public static $TEMPLATECONTENT = "<!-- ###TEMPLATE_CONTENT### -->";
    public static $TEMPLATEFOOTER = "<!-- ###TEMPLATE_FOOTER### -->";

    public static $TEMPLATEBEGIN = "<!-- ###BEGIN_TEMPLATE### -->";
    public static $TEMPLATEEND = "<!-- ###END_TEMPLATE### -->";

    public static $TEMPLATEFOREACHBEGIN = "FOREACHBEGIN";
    public static $TEMPLATEFOREACHEND = "FOREACHEND";

    public static $TEMPLATEIF= "IF";
    public static $TEMPLATEIFELSE = "ELSE";
    public static $TEMPLATEIFEND = "ENDIF";

    public static $BEGIN = "<!-- ###BEGIN### -->";
    public static $END = "<!-- ###END### -->";

}

?>