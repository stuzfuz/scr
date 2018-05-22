<?php

class RegisterStep2GetController extends SimpleController {

    protected function gatherData() {
        // check if required request params are set
        \Logger::logDebugPrintR("'RegisterStep2GetController::gatherData()'   [" . __LINE__ ."]  GET  route =   ", $this->route); 
            
        //TODO if the user is not logged here, then something went wrong while registering
        // handle this differently from redirecting to /
        // user logged in? NO -> then redirect to /
        if (!\AuthenticationManager::isAuthenticated()) {
            // die("in RegisterStep2GetController - user is NOTNOTNOT logged in:-( ");
            \Logger::logDebugPrintR("'RegisterStep2GetController::gatherData()' [" . __LINE__ ."]  user should be logged -> cant continue :-(  ", ""); 
            \Util::quit500("RegisterStep2GetController::gatherData()   [" . __LINE__ ."] user should be logged at this point of time  -> cant continue :-(   ", "");
            // \Util::redirect("/");
        }

        // get the user object from the session
        $user = \AuthenticationManager::getAuthenticatedUser();
        
        if ($user == null) {
            \Logger::logDebugPrintR("'RegisterStep2GetController::gatherData()' [" . __LINE__ ."]  this shouldn't be happending user should be logged in ", ""); 
            \Util::quit500("RegisterStep2GetController::gatherData()   [" . __LINE__ ."]    this shouldn't be happending user should be logged in   ", "");
        }
        // load all available channels
        $data = \DatabaseManager::getAllChannels();

        // add addiotnal data for the template
        $data["username"] = '@' . $user->getUserName();
        $data["userid"] = $user->getId();
        $data["loggedin"] = true;

        $data["stepone"] = 0;   // false
        $this->data = $data; 
        
        \Logger::logDebugPrintR("'RegisterStep2Controller::gatherData()' [" . __LINE__ ."]  GET  this->data   ", $this->data); 
    }

    public function justDoIt() : string {
        self::gatherData();
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}
