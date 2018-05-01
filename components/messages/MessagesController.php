<?php

class MessagesController extends SimpleController {

    protected function gatherData() {

        \Logger::logDebugPrintR("'MessagesController::gatherData()' [" . __LINE__ ."]  route =   ", $this->route); 
        // echo "<br> MessagesController - in 'gather data'";
        // \Util::my_var_dump($this->db_conn, "MessagesController this->db_conn  = ");

        // TODO: only those for the user who is logged in
        $sql = "SELECT id, name FROM channel WHERE deleted = 0 ORDER BY name";
        $res = \DatabaseManager::query($this->db_conn, $sql, array());
        //  \Util::my_var_dump($res, "res = ");
        // exit();

        // echo "<br> MessagesController - in 'gather data' after  query";
        $channels = array();
        $data = array(); 
        if ($res->rowCount() == 0) {
            $data["channelsfound"] = false; 
        } else {
            // echo "<br><br> adding channels to array ... <br>";
            while ($channel = \DatabaseManager::fetchAssoz($res)) {
                // \Util::my_var_dump($channel, "MessagesController channel  = ");
                $channel["nameasurl"] = urlencode($channel["name"]);
                $channels[] = $channel; 
            }
            
            $data["channelsfound"] = true; 
            $data["channels"] = $channels; 
        }

        // if (isset($this->route["routeparam"]) &&  $this->route["routeparam"] != null) {
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

        //  \Util::my_var_dump($res, "res = ");
        // exit();

        // // echo "<br> MessagesController - in 'gather data' after  query";
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
 