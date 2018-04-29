<?php
declare(strict_types = 1);
error_reporting(E_ALL);

// ist anscheinend in centos defaultmäßig auf off
ini_set('display_errors', 'on');
setlocale(LC_MONETARY, 'de_AT');

// https://docs.acquia.com/article/basic-rewrite-rule-examples
// http.conf AllowOverride All

echo "hello from index.php -> the path used was:  ?? <br/><br/><br/>";

var_dump($_GET);
echo "<br/><br/>";
// var_dump($_POST);echo "<br/><br/>";
// var_dump($_REQUEST);echo "<br/><br/>";

echo "SERVER[REQUEST_URI]:      ";

var_dump($_SERVER['REQUEST_URI']);
echo "<br/><br/>";

echo "SERVER[REDIRECT_URL]:      ";
var_dump($_SERVER['REDIRECT_URL']);
echo "<br/><br/>";

?>
