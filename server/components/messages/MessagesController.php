<?php

class MessagesController extends SimpleController {

    protected function gatherData() {

        \Logger::logDebugPrintR("'MessagesController::gatherData()' [" . __LINE__ ."]  route =   ", $this->route); 

        // user logged in? NO -> then redirect to /
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $data["username"] = '@' . $user->getUserName();
            $data["loggedin"] = true;
        } else {
            \Util::redirect("/");
        }
        $data = [];

        $tmp = \DatabaseManager::getChannelsForUser($user->getId());
        
        $data = array_merge($data, $tmp);

        // if a channelname is provided in the URL - load the data 
        // check if the channelname really exists!
        if (isset($this->route["channelname"])) {
            $topicsAndMessages = \DatabaseManager::getTopicsAndMessagesForUser($user->getId(), $this->route["channelname"]);
        } else {
            \Logger::logDebugPrintR("'MessagesController::gatherData()' [" . __LINE__ ."]  no routeparam provided  ", ""); 
            \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] no routeparam provided   ", "");
        }

        $data = array_merge($data, $topicsAndMessages);
        $data["channelname"] = $this->route["channelname"];

        \Logger::logDebugPrintR("'MessagesController::gatherData()' [" . __LINE__ ."]  data with topics and messages =   ", $data);
 


        
        $this->data = $data; 
        \Logger::logDebugPrintR("MessagesController this->data  = ", $this->data);
    }

    // // TODO Delete this if not necessary
    public function justDoIt() : string {
        // echo "<br> MessagesController - in 'justDoIt'";
        self::gatherData();
        // echo "<br>  MarinController $this->route['headertemplate']  " . $this->route['headertemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['contenttemplate']  " . $this->route['contenttemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['footertemplate']  " . $this->route['footertemplate'] . "<br/>";

        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}
 