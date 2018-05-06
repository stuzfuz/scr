<?php

SessionContext::create();

class AuthenticationManager {

    public static function authenticate(string $username, string $password) : bool {

        // \Logger::logDebugPrintR("AuthenticationManager::authenticate  username = $username, password = $password  ", "");

        $user = \DatabaseManager::getUserByUserName($username);
        // TODO: change back to hash('sha1', "$username|$password")
        // \Logger::logDebugPrintR("AuthenticationManager::authenticate  user =   ", $user);
        if ($user != null && $user->getPassword() == hash('sha1', "$username|$password")) {
            $_SESSION['user'] = $user->getId();
            return true;
        }
        self::signOut();
        return false;
    }
    
    public static function signOut()  {
        unset($_SESSION['user']);
    }

    public static function isAuthenticated() : bool {
        // die("in isAuthenticated() ...");     
        return isset($_SESSION['user']);
    }

    public static function getAuthenticatedUser()  {
        return self::isAuthenticated() ? \DatabaseManager::getUserById($_SESSION['user']) : null;
    }
}
