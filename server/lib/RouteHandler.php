<?php

class RouteHandler {

    private static $requestParams = null;
    private static $requestPath = null; 

    private static function sanitizeRequestParams() {
        \Logger::logDebugPrintR("'RouteHandler::sanitizeRequestParams()' [" . __LINE__ ."]  _REQUEST =   ", $_REQUEST); 
        foreach ($_REQUEST as $key => $param) {
            self::$requestParams[$key] = \Util::escape($param);
        }
        \Logger::logDebugPrintR("'RouteHandler::sanitizeRequestParams()' [" . __LINE__ ."]  xxx requestParams =   ", self::$requestParams); 
    }

    private static function sanitizeURL() {
        self::$requestPath = $_SERVER['REDIRECT_URL'];
        //TODO  sanitize $_SERVER['REDIRECT_URL'] 
    }

    public static function handleRoute($db_conn)  {
        self::sanitizeURL();

        $verb = $_SERVER['REQUEST_METHOD'];
        $requestPath2 = self::$requestPath;

        if ($verb == "POST") {
            self::sanitizeRequestParams();
        }

        $sql = "SELECT * FROM route WHERE verb = ? AND route = ?";
        $params = array($verb);
        $params[] = self::$requestPath;
        $param = null;
        // possible parameter in path
        if (substr_count(self::$requestPath, '/') > 1) {
            $pos = strrpos(self::$requestPath, "/");
            $requestPath2 = substr(self::$requestPath, 0, $pos);
            $param = substr(self::$requestPath, $pos+1);

            // \Util::my_var_dump($requestPath, "requestpath = ");
            // \Util::my_var_dump($requestPath2, "requestpath2 = ");
            $sql = "SELECT * FROM route WHERE verb = ? AND (route = ? OR (route = ? AND routeparam IS NOT NULL))";
            $params[] = $requestPath2;
        }
        $res = \DatabaseManager::query($db_conn, $sql, $params);
       //  \Util::my_var_dump($res, "res = ");
        // exit();

        if ($res->rowCount() == 0) {
            if ($_GET) {
                \Logger::logWarning("404 - could not find page: " , $self::$requestPath);
                readfile('static/404.html');
                exit();
            }
        } else {
            $route = \DatabaseManager::fetchAssoz($res);
            // \Util::my_var_dump($route, "route = ");
            // \Logger::logDebugPrintR("'RouteHandler::handleRoute()' [" . __LINE__ ."]  route =   ", $route); 
            // \Logger::logDebugPrintR("'RouteHandler::handleRoute()' [" . __LINE__ ."]  param =   ", $param); 

            if ($param != null) {
                $route[$route["routeparam"]] = urldecode($param);
            }
            $route["requestparameter"] = self::$requestParams;
        }

        // \Logger::logDebugPrintR("'RouteHandler::handleRoute()' [" . __LINE__ ."]  route =   ", $route);

        return $route; 
    }
}
