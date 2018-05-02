<?php

class SignoutController extends SimpleController {

    protected function gatherData() {
        // \Logger::logDebugPrintR("SignoutController this->data  = ", $this->data);
    }

    public function justDoIt() : string {
        
        \Logger::logDebugPrintR("SignoutController::justDoIt() signing out ", "");
        
        \AuthenticationManager::signOut();
        \Util::redirect("/");
    }    
}
 