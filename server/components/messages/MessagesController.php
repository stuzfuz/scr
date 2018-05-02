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

        $tmp = \DatabaseManager::getChannelsForUser($user->getId());
        
        $data = array_merge($data, $tmp);

        // if a channelname is provided in the URL - load the data 
        // check if the channelname really exists!
        if (isset($this->route["channelname"])) {
            $sql = "SELECT user.id AS userid, user.username, message.txt, message.created_at FROM message ";
            $sql .= "LEFT JOIN channel ON (channel.id = message.channel_id) ";
            $sql .= "LEFT JOIN user ON (message.user_id = user.id) ";
            $sql .=  "WHERE  channel.name = ?  AND message.deleted = 0  AND channel.deleted = 0";
        } else {
            \Logger::logDebugPrintR("'MessagesController::gatherData()' [" . __LINE__ ."]  no routeparam provided  ", ""); 
            \Util::quit500("Fatal Error - 'traverseAST' [" . __LINE__ ."] no routeparam provided   ", "");
        }
        $res = \DatabaseManager::query($this->db_conn, $sql, array($this->route["channelname"]));
        \Logger::logDebugPrintR("'MessagesController::gatherData()' [" . __LINE__ ."]  res =   ", $res);
 
        if ($res->rowCount() == 0) {
            // TODO: show info -> channelname not found
        }
        
        // TODO read meta dat of channel and add those here 
        $data["channelname"] = $this->route["channelname"];


        // read all message from this channel
        $messages = array();
        if ($res->rowCount() == 0) {
            $data["messagesfound"] = 0;     // false does not  work ...
            $data["messages"] = array(); 
        }  else {
            $messages = array();
            // echo "<br><br> adding channels to array ... <br>";
            while ($msg = \DatabaseManager::fetchAssoz($res)) {
                \Logger::logDebugPrintR("MessagesController msg = ", $msg);
                $messages[] = $msg; 
            }
            $data["messagesfound"] = true; 
            $data["messages"] = $messages; 
        }
        
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
 