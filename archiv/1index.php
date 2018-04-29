<?php

require_once('inc/bootstrap.php');

$default_view = 'welcome';

$view = $default_view;

if (isset($_REQUEST['view']) && file_exists(__DIR__ . '/views/' . $_REQUEST['view'] . '.php')) {
    $view = $_REQUEST['view'];
}

$postAction = isset($_REQUEST[\BookShop\Controller::ACTION]) ? 
                    $_REQUEST[\BookShop\Controller::ACTION] : null; 
if ($postAction != null) {
    \BookShop\Controller::getInstance()->invokePostAction();
}


// $b = new Bookshop\Book(12, 1, 'test', 'testauthr', 12.95);
// // // $e = new Bookshop\Entity(23);
// // echo $b->title; 

// var_dump($b);

// $c = new BookShop\Category(1, 'test');

// $u = new BookShop\User(2322, 'Äaffsfsfssfsf', 'hash');

// echo '<br/>';
// var_dump($c);
// echo '<br/>';
// var_dump($u);
// echo '<br/>';


/* load view */
require_once ('views/' . $view . '.php');


