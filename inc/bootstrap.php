<?php 

// PHP 7 parametrisierte Methoden schnittstellen und 
// Returnwerte, sie Typsicher sind
declare(strict_types = 1);
error_reporting(E_ALL);

// ist anscheinend in centos defaultmäßig auf off
ini_set('display_errors', 'on');
setlocale(LC_MONETARY, 'de_AT');

// very simple class autoloader    // spl: standard php library
// autoload triggert, wenn irgendwo ein Object instantiert wird also zB b = new Book()
spl_autoload_register(function ($Class) {
    // __DIR__ wo das aktuelle file liegt (bootstrap in 'inc')
    echo $Class . '<br/>'; 
    echo DIRECTORY_SEPARATOR . '<br/>'; 
    $filename = __DIR__ . '/../lib/' .  str_replace('\\', DIRECTORY_SEPARATOR, $Class) . '.php';
    echo $filename; 
    if (file_exists($filename)) {
        include($filename);
    }
});
