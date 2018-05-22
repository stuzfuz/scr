<?php

class SignoutController extends SimpleController {
    public function justDoIt() : string {
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            \Logger::logAccess($user->getId(), "signout");            
        } 
        \Logger::logDebugPrintR("SignoutController::justDoIt() signing out ", "");
        \AuthenticationManager::signOut();
        \Util::redirect("/");
    }    
}
 