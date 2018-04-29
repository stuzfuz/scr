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
		if ($page == null) {
			$page = isset($_REQUEST[Controller::PAGE]) ?
				$_REQUEST[Controller::PAGE] :
				$_SERVER['REQUEST_URI'];
		}
		header("Location: $page");
		exit();
  }
  
  public static function my_var_dump($var, string $info = null) {
    echo "<br>" . $info . "<br><pre>";
    var_dump($var);
    echo "</pre>";
  }

  
}