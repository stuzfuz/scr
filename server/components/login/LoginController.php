<?php

class LoginController extends SimpleController {
    protected function gatherData() {
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            \Util::redirect("/");
        } 
        \Logger::logAccess(-1, "/login");
    }
}
 