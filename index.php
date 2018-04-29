<?php

require_once('inc/bootstrap.php');



echo "hello from index.php -> the path used was:  " . $_SERVER['REQUEST_URI'] ."<br/><br/><br/>";

// var_dump($_GET);
// echo "<br/><br/>";
// var_dump($_POST);echo "<br/><br/>";
// var_dump($_REQUEST);echo "<br/><br/>";

// echo "SERVER[REQUEST_URI]:      ";

// var_dump($_SERVER['REQUEST_URI']);
// echo "<br/><br/>";

// echo "SERVER[REDIRECT_URL]:      ";
// var_dump($_SERVER['REDIRECT_URL']);
// echo "<br/><br/>";

// echo "connecting to DB <br/>";
$db_conn = \DatabaseManager::getConnection();
// echo "<br/>AFTER connecting to DB <br/>";

$route = \RouteHandler::handleRoute($db_conn);

// \Util::my_var_dump($route , "in index.php");

require_once($route['controller'] . $route['controllername'] . ".php");

$controllerName = $route['controllername'];

$ctrl = new $controllerName('hello');

echo "<h2>" . $ctrl->justDoIt() . "</h2>";

?>
