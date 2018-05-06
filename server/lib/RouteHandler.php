<?php

class RouteHandler {

    private static $requestParams = null;
    private static $requestPath = null; 

    private static function sanitizeRequestParams() {
        // \Logger::logDebugPrintR("'RouteHandler::sanitizeRequestParams()' [" . __LINE__ ."]  _REQUEST =   ", $_REQUEST); 
        foreach ($_REQUEST as $key => $param) {
            if (is_array($param)) {
                foreach ($param as $key2 => $param2)  {
                    self::$requestParams[$key][$key2] = \Util::escape($param2);
                }
            } else {
                self::$requestParams[$key] = \Util::escape($param);
            }
            
        }
        // \Logger::logDebugPrintR("'RouteHandler::sanitizeRequestParams()' [" . __LINE__ ."]  xxx requestParams =   ", self::$requestParams); 
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

        // \Logger::logDebugPrintR("'RouteHandler::handleRoute()' [" . __LINE__ ."] verb =  $verb ,  self::requestPath  ", self::$requestPath);

        $sql = "SELECT * FROM route WHERE verb = ? AND route = ?";
        $params = array($verb);
        $params[] = self::$requestPath;
        $param = null;
        // possible parameter in path
        if (substr_count(self::$requestPath, '/') > 1) {
            $pos = strrpos(self::$requestPath, "/");
            $requestPath2 = substr(self::$requestPath, 0, $pos);
            $param = substr(self::$requestPath, $pos+1);

            $sql = "SELECT * FROM route WHERE verb = ? AND (route = ? OR (route = ? AND routeparam IS NOT NULL))";
            $params[] = $requestPath2;
        }
        $res = \DatabaseManager::query($db_conn, $sql, $params);
       //  \Util::my_var_dump($res, "res = ");
        // exit();

        // \Logger::logDebugPrintR("'RouteHandler::handleRoute()' [" . __LINE__ ."]  res from db query =   ", $res);
        $route = null; 
        if ($res->rowCount() == 0) {
            // \Logger::logDebugPrintR("'RouteHandler::handleRoute()' [" . __LINE__ ."]  rowcoint is zero    ", "");

            if ($verb === "GET") {
                \Logger::logDebug("404 - could not find page: " , self::$requestPath);
                \Util::quit404("404 - could not find requested URL " , self::$requestPath);
            }
            if ($verb ==="POST") {
                \Logger::logDebug("'RouteHandler::handleRoute()' [" . __LINE__ ."]  could not find request URL - POST combiantion   ", self::$requestPath); 
                \Util::quit500("500 - could not find requested URL " , self::$requestPath);                
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
