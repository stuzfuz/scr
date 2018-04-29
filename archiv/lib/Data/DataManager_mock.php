<?php

namespace Data; 

use BookShop\Category;
use BookShop\Book;
use BookShop\User;

class DataManager {

    private static function getMockData(string $type) : array {

        $data = array();
        switch ($type) {
            case 'categories':
              $data = [
                1 => new Category(1, "Mobile & Wireless Computing"),
                2 => new Category(2, "Functional Programming"),
                3 => new Category(3, "C / C++"),
                4 => new Category(4, "<< New Publications >>"),
              ];
              break;
            case 'books':
              $data = [
                1  => new Book(1, 1, "Hello, Android:\nIntroducing Google's Mobile Development Platform", "Ed Burnette", 19.97),
                2  => new Book(2, 1, "Android Wireless Application Development", "Shane Conder, Lauren Darcey", 31.22),
                5  => new Book(5, 1, "Professional Flash Mobile Development", "Richard Wagner", 19.90),
                7  => new Book(7, 1, "Mobile Web Design For Dummies", "Janine Warner, David LaFontaine", 16.32),
                11 => new Book(11, 2, "Introduction to Functional Programming using Haskell", "Richard Bird", 74.75),
                //book with bad title to show scripting attack - add for scripting attack demo only
                12 => new Book(12, 2, "Scripting (Attacks) for Beginners - <script type=\"text/javascript\">alert('All your base are belong to us!');</script>", "John Doe", 9.99),
                14 => new Book(14, 2, "Expert F# (Expert's Voice in .NET)", "Antonio Cisternino, Adam Granicz, Don Syme", 47.64),
                16 => new Book(16, 3, "C Programming Language\n(2nd Edition)", "Brian W. Kernighan, Dennis M. Ritchie", 48.36),
                27 => new Book(27, 3, "C++ Primer Plus\n(5th Edition)", "Stephan Prata", 36.94),
                29 => new Book(29, 3, "The C++ Programming Language", "Bjarne Stroustrup", 67.49),
              ];
              break;
            case 'users':
              $data = [
                1 => new User(1, "scm4", "a8af855d47d091f0376664fe588207f334cdad22"), //USER = scm4; PASSWORD = scm4
              ];
				      break;
        }
        return $data;

    }

    public static function getCategories() : array {
        // in statischen Methoden ist self referenz auf eigene Klasse
        return self::getMockData('categories');
    }

    public static function getBooksByCategory(int $categoryId) : array {
      $res = array();
      foreach(self::getMockData('books') as $book) {
        if ($book->getCategoryId() === $categoryId) {
          $res[] = $book;
        }
      }
      return $res;
    }

    public static function getUserByUserName(string $userName) {
      foreach(self::getMockData('users') as $u) {
        if ($u->getUserName() == $userName) {
          return $u;
        }
      }
      return false;
    }


    public static function getUserById(int $userId) {
      return array_key_exists($userId, self::getMockData('users')) ? self::getMockData('users')[$userId] : null;
    }


}
