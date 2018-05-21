<?php

class ChannelController extends SimpleController {

    protected function gatherData() {

        // \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  route =   ", $this->route); 
        
        // user logged in? NO -> then redirect to /
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $data["username"] = '@' . $user->getUserName();
            $data["isloggedin"] = true;
        } else {
            \Util::redirect("/");
        }

        $tmp = \DatabaseManager::getChannelsForUser($user->getId());
        $data = array_merge($data, $tmp);

        $channelId = \DatabaseManager::getChannelIdForName($this->route["channelname"]);

        // if a channelname is provided in the URL - load the data 
        // check if the channelname really exists!
        if (isset($this->route["channelname"])) {
            $topicsAndMessages = \DatabaseManager::getMessagesForUser($user->getId(), $this->route["channelname"]);
        } else {
            \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  no routeparam provided  ", ""); 
            \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] no routeparam provided   ", "");
        }
        // \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  topicsAndMessages =   ", $topicsAndMessages);
        
        $data = array_merge($data, $topicsAndMessages);

        $data["channelname"] = $this->route["channelname"];
        $data["channelid"] = $channelId; 
        // \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  data =   ", $data);

        // $data["hasimportanttopics"] = 0;
        // $data["hastopics"] = 0;
        $this->data = $data; 
        \Logger::logDebugPrintR("'ChannelController::gatherData()' [" . __LINE__ ."]  data   = ", $this->data);
    }

    // // TODO Delete this if not necessary
    public function justDoIt() : string {
        self::gatherData();
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}
 