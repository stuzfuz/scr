<?php

namespace Data; 

use BookShop\Category;
use BookShop\Book;
use BookShop\User;

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
                echo $e->getMessage();
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

    public static function getCategories() : array {
        $categories = array();
        $con = self::getConnection();
        
        $res = self::query($con, "SELECT id, name FROM categories");

        // iterate over cursor
        while ($cat = self::fetchObject($res)) {
            $categories[] = new Category($cat->id, $cat->name);
        }

        self::closeConnection();

        return $categories;
    }

    public static function getBooksByCategory(int $categoryId) : array {
        $books = array();
        $con = self::getConnection();
        $res = self::query($con, "SELECT id, categoryId, title, author, price 
            FROM books WHERE categoryId = ?", array($categoryId));

        // var_dump($res);
        while($book = self::fetchObject($res)) {
            //var_dump($book);
            $books[] = new Book($book->id, $book->categoryId, $book->title, $book->author, $book->price);
        }
        self::closeConnection();
        return $books;
    }

    public static function getUserByUserName(string $userName) {
      $user = null; 
      $con = self::getConnection();
      $res = self::query($con, "SELECT id, userName, passwordHash FROM users WHERE username = ?", array($userName));
      if ($u = self::fetchObject($res)) {
          $user = new User($u->id, $u->userName, $u->passwordHash);
      }
      self::closeConnection();
      return $user;
    }

    public static function getUserById(int $userId) {
        $user = null; 
      $con = self::getConnection();
      $res = self::query($con, "SELECT id, userName, passwordHash FROM users WHERE id = ?", array($userId));
      if ($u = self::fetchObject($res)) {
          $user = new User($u->id, $u->userName, $u->passwordHash);
      }
      self::closeConnection();
      return $user;
    }

    public static function createOrder(int $userId, array $bookIds, string $nameOnCart, string $cardNumber) : int {
        $con = self::getConnection();
        $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        $con->beginTransaction();
        try {
            self::query("INSERT INTO orders (userid, creditCardNumber, creditCardHolder) VALUES (?, ?, ?)",
                array($userId, $cardNumber, $nameOnCart));

            $orderId = $con->lastInsertid();

            foreach($bookIds as $bookId) {
                self::query($con, "INSERT INTO orderedbooks (orderId, bookId) VALUES (?, ?) ",
                    array($orderId, $bookId));
            }

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $orderId = null;
        }
        self::closeConnection();
        return $orderId;
    }
}
