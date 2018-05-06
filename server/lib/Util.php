<?php

class Util {

  /**
   * bereinigt den output
   *
   * @param string $string  der string
   * @return string
   */
  public static function escape(string $string) : string {
    return nl2br(htmlentities($string));
  }

  /**
   * redirect mit optionaler url - HINWEIS - redirection attack möglich!
   *
   * @param string $page  uri optional
   * @return null
   */
	public static function redirect(string $page = null) {
    // die("redirect NOT IMPLEMENTED ");
		// if ($page == null) {
		// 	$page = isset($_REQUEST[Controller::PAGE]) ?
		// 		$_REQUEST[Controller::PAGE] :
		// 		$_SERVER['REQUEST_URI'];
		// }
		header("Location: $page");
		exit();
  }
  
  // public static function my_var_dump($var, string $info = null) {
  //   // echo "<br>" . $info . "<br><pre>";
  //   // var_dump($var);
  //   // echo "</pre>";

  //   // watch the log file ...
  //   \Logger::logDebug("my_var_dump: $info ", var_dump($$var));
  // }

  public static function quit500($msg1, $msg2) {
    \Logger::logError($msg1,  $msg2);
    http_response_code(500);
    readfile('client/static/500.html');
    exit();
  }

  public static function quit404($msg1, $msg2) {
    \Logger::logError($msg1,  $msg2);
    http_response_code(404);
    readfile('client/static/404.html');
    exit();
  }
}