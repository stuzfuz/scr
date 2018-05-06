<?php

class SignoutController extends SimpleController {
    public function justDoIt() : string {
        
        \Logger::logDebugPrintR("SignoutController::justDoIt() signing out ", "");
        
        \AuthenticationManager::signOut();
        \Util::redirect("/");
    }    
}
 