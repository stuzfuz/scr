<?php

class MainController extends SimpleController {

    protected function gatherData() {
        $ret = null; 
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $ret["username"] = '@' . $user->getUserName();
            $ret["isloggedin"] = true;
        } else {
            $ret["isloggedin"] = false;
            $this->data = $ret; 
            return; 
        }
       
        $tmp = \DatabaseManager::getChannelsForUser($user->getId());
        // \Logger::logDebugPrintR("'MainController::gatherData()' [" . __LINE__ ."]  channels for user  ", $tmp); 

        // $ret= \DatabaseManager::getChannelsForUser($user->getId());

        $bla = array_merge($tmp, $ret);
        $this->data = $bla; 

        // \Logger::logDebugPrintR("'MainController::gatherData()' [" . __LINE__ ."]  merged arrays  ", $bla); 
 
        \Logger::logDebugPrintR("'MainController::gatherData()' [" . __LINE__ ."]  this->data  ", $this->data); 
        // \Logger::logDebugPrintR("'MainController::gatherData()' [" . __LINE__ ."]  ret  ", $ret); 

    }

    // // TODO Delete this if not necessary
    public function justDoIt() : string {
        // echo "<br> MainControiller - in 'justDoIt'";
        self::gatherData();
        // echo "<br>  MarinController $this->route['headertemplate']  " . $this->route['headertemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['contenttemplate']  " . $this->route['contenttemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['footertemplate']  " . $this->route['footertemplate'] . "<br/>";

        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}
 