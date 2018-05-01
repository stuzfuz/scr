<?php

require_once('server/inc/bootstrap.php');

$db_conn = \DatabaseManager::getConnection();

$route = \RouteHandler::handleRoute($db_conn);

require_once($route['controller'] . $route['controllername'] . ".php");

$controllerName = $route['controllername'];

$ctrl = new $controllerName($db_conn, $route);

echo   $ctrl->justDoIt();

?>
