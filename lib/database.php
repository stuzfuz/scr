<?php

namespace Data; 

use inc\config;

class DataManager {

    private static $__connection;   // $connection   das __ heißt private

    private static function getConnection() {
        
        if (!isset(self::$__connection)) {
            //echo "connecting to database";
            try {
                self::$__connection = new \PDO('mysql:unix_socket=/var/lib/mysql/mysql.sock;dbname=testdb;dbname=fh_scm4_bookshop;charset=utf8', 'root', 'Thorin');
                // echo 'Connected to database';
            }
            catch(PDOException $e)
            {
                Logger::log("Fatal Error - could not connect to Database");
                readfile('static/500.html');

            }
        }
        // var_dump(self::$__connection);
        if (self::$__connection  == null) {
            die("no connection");
        }
        return self::$__connection;
    }

    private static function query($connection, $query, $parameters = array()) {
        $statement = $connection->prepare($query);
        $i = 1; 
        foreach($parameters as $param) {
            if (is_int($param)) {
                $statement->bindValue($i, $param, \PDO::PARAM_INT);

            }
            if (is_string($param)) {
                $statement->bindValue($i, $param, \PDO::PARAM_STR);
            }
            $i++;
        }

        $statement->execute();

     var_dump($statement->debugDumpParams());
        return $statement; 
    }

    private static function fetchObject($cursor) {
        return $cursor->fetchObject();
    }

    private static function closeConnection() {
        self::$__connection == null; 
    }
}
