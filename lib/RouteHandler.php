<?php

class RouteHandler {

    public static function handleRoute($db_conn)  {
        $requestPath = $_SERVER['REDIRECT_URL'];
        $verb = $_SERVER['REQUEST_METHOD'];
        $requestPath2 = $requestPath;

        $sql = "SELECT * FROM route WHERE verb = ? AND route = ?";
        $params = array($verb);
        $params[] = $requestPath;
        // possible parameter in path
        if (substr_count($requestPath, '/') > 1) {
            $pos = strrpos($requestPath, "/");

            $requestPath2 = substr($requestPath, 0, $pos);

            // \Util::my_var_dump($requestPath, "requestpath = ");
            // \Util::my_var_dump($requestPath2, "requestpath2 = ");
            $sql = "SELECT * FROM route WHERE verb = ? AND (route = ? OR (route = ? AND routeparam IS NOT NULL))";
            $params[] = $requestPath2;
        }
        $res = \DatabaseManager::query($db_conn, $sql, $params);
       //  \Util::my_var_dump($res, "res = ");
        // exit();

        if ($res->rowCount() == 0) {
            \Logger::logWarning("404 - could not find page: " , $requestPath);
            readfile('static/404.html');
            exit();
        } else {
            $route = \DatabaseManager::fetchAssoz($res);
            // \Util::my_var_dump($route, "route = ");
        }

        return $route; 
    }
}
