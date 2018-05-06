<?php

class LoginController extends SimpleController {

    protected function gatherData() {
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            \Util::redirect("/");
        } 
    }
    // public function justDoIt() : string {
    //     // self::gatherData();
    //     return '';
    // }   
}
 