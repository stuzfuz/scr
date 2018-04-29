<?php

namespace BookShop;

class Controller extends BaseObject {
    
    const ACTION = 'action';
    const PAGE = 'page';

    const ACTION_ADD = 'addToCart';
    const ACTION_REMOVE = 'removeToCart';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const USER_NAME = 'userName';
    const USER_PASSWORD = 'password';

    private static $instance = false; 

    private function __construct() {}
    
    // singleton pattern
    public static function getInstance() : Controller {
        if (!self::$instance) {
            self::$instance = new \BookShop\Controller();
        } 
        return self::$instance;
    }

    public function invokePostAction(): bool {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new Exception("POST requests only");
            return null;
        } elseif (!isset($_REQUEST[self::ACTION]))   {
            throw new Exception (self::ACTION . ' not defined');
            return null;
        }

        $action = $_REQUEST[self::ACTION];

        switch ($action) {
            case self::ACTION_ADD: 
                // var_dump($_REQUEST);
                \BookShop\ShoppingCart::add((int) $_REQUEST['bookId']);
                Util::redirect();
                break;

            case self::ACTION_REMOVE: 
                // var_dump($_REQUEST);
                \BookShop\ShoppingCart::remove((int) $_REQUEST['bookId']);
                Util::redirect();
                break;

            case self::ACTION_LOGIN: 
                // var_dump($_REQUEST);
                if (!\BookShop\AuthenticationManager::authenticate(
                    $_REQUEST[self::USER_NAME], $_REQUEST[self::USER_PASSWORD])) {
                    $this->forwardRequest(array("Invalid credentials"));
                }
                Util::redirect();
                break;

            case self::ACTION_LOGOUT: 
                \BookShop\AuthenticationManager::signOut();
            
                Util::redirect();
                break;
        }
        // return true;
    }

    /**
   * 
   * @param array $errors : optional assign it to 
   * @param string $target : url for redirect of the request
   */
  protected function forwardRequest(array $errors = null, $target = null) {
    //check for given target and try to fall back to previous page if needed
    if ($target == null) {
      if (!isset($_REQUEST[self::PAGE])) {
        throw new Exception('Missing target for forward.');
      }
      $target = $_REQUEST[self::PAGE];
    }
    //forward request to target
    // optional - add errors to redirect and process them in view
    if (count($errors) > 0)
      $target .= '&errors=' . urlencode(serialize($errors));
    header('location: ' . $target);
    exit();
  }

}