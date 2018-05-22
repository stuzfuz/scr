<?php

class ChannelController extends SimpleController {

    protected function gatherData() {        
        // user logged in? NO -> then redirect to /
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $data["username"] = '@' . $user->getUserName();
            $data["isloggedin"] = true;
        } else {
            \Util::redirect("/");
        }

        \Logger::logAccess($user->getId(), "view channels");

        $tmp = \DatabaseManager::getChannelsForUser($user->getId());
        $data = array_merge($data, $tmp);

        if (!isset($this->route["channelname"])) {
            $data["channelselected"] = false; 
            $this->data = $data; 
            return;
        }

        $channelId = \DatabaseManager::getChannelIdForName($this->route["channelname"]);
        \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  channelId = $channelId",""); 

        if (!$channelId) {
            $data["channelselected"] = false; 
            $this->data = $data; 
            return;
        }
        // if a channelname is provided in the URL - load the data 
        // check if the channelname really exists!
        if (isset($this->route["channelname"])) {
            $messages = \DatabaseManager::getMessagesForUser($user->getId(), $this->route["channelname"]);
            $data["channelselected"] = true;
        } else {
            $data["channelselected"] = false;
            $this->data = $data; 
            return;
            // \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  no routeparam provided  ", ""); 
            // \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] no routeparam provided   ", "");
        }
        // \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  messages =   ", $messages);
        
        $data = array_merge($data, $messages);
       
        $data["channelname"] = $this->route["channelname"];
        $data["channelid"] = $channelId; 
        // \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  data =   ", $data);

        // $data["hasimportanttopics"] = 0;
        // $data["hastopics"] = 0;
        $this->data = $data; 
        \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  data   = ", $this->data);
    }

    public function justDoIt() : string {
        self::gatherData();
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}
 