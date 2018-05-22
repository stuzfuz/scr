<?php

class ApplicationConfig {

    public static $databaseName = 'fh_2018_scm4_S1610307036';
    public static $databaseUsername = 'fh_2018_scm4';
    public static $databasePassword = 'fh_2018_scm4';
    public static $databaseHost = 'mariadb';

    public static $logFile = 'server/log/poormansslack.log';
    public static $logFileError = 'server/log/err_poormansslack.log';
    public static $logFileDebug = 'server/log/debug_poormansslack.log';
    public static $logQuery = 'server/log/query_poormansslack.log';
    public static $logAccess = 'server/log/access_poormansslack.log';

    public static $indexTmpl = 'client/index.tmpl';
    
    public static $TEMPLATEHEADER = "<!-- ###TEMPLATE_HEADER### -->";
    public static $TEMPLATECONTENT = "<!-- ###TEMPLATE_CONTENT### -->" ;
    public static $TEMPLATEFOOTER = "<!-- ###TEMPLATE_FOOTER### -->";

    public static $TEMPLATEBEGIN = "<!-- ###BEGIN_TEMPLATE### -->";
    public static $TEMPLATEEND = "<!-- ###END_TEMPLATE### -->";

    public static $PARTIALBEGIN = "<!-- ###BEGIN_PARTIAL### -->";
    public static $PARTIALEND = "<!-- ###END_PARTIAL### -->";
}

?>