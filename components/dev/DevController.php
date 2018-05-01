<?php

class DevController extends SimpleController {

    protected function gatherData() {
        // echo "<br> DevController - in 'gather data'";

        // \Util::my_var_dump($this->db_conn, "MainController this->db_conn  = ");

        // TODO: only those for the user who is logged in
        $sql = "SELECT id, name  FROM channel WHERE deleted = 0 ORDER BY name";
        $res = \DatabaseManager::query($this->db_conn, $sql, array());
        //  \Util::my_var_dump($res, "res = ");
        // exit();

        // echo "<br> DevController - in 'gather data' after  query";
        $channels = array();
        $data = array(); 
        if ($res->rowCount() == 0) {
            $data["channelsfound"] = false; 
        } else {
            // echo "<br><br> adding channels to array ... <br>";
            while ($channel = \DatabaseManager::fetchAssoz($res)) {
                // \Util::my_var_dump($channel, "MainController channel  = ");
                $channels[] = $channel; 
            }
            
            $data["channelsfound"] = true; 
            $data["channels"] = $channels; 
        }

        $sql = "SELECT id, txt, user_id FROM message WHERE deleted = 0 ";
        $res = \DatabaseManager::query($this->db_conn, $sql, array());
        //  \Util::my_var_dump($res, "res = ");
        // exit();

        // // echo "<br> DevController - in 'gather data' after  query";
        $messages = array();
        if ($res->rowCount() == 0) {
            $data["messagesfound"] = false; 
        }  else {
            // echo "<br><br> adding channels to array ... <br>";
            while ($msg = \DatabaseManager::fetchAssoz($res)) {
                // \Util::my_var_dump($channel, "MainController channel  = ");
                $messages[] = $msg; 
            }
            
            $data["messagesfound"] = true; 
            $data["messages"] = $messages; 
        }
        $this->data = $data; 
        \Logger::logDebugPrintR("DevCOntroller::gatherData()  [".  __LINE__  . "]  $data  = ", $data);
    }

    // // TODO Delete this if not necessary
    public function justDoIt() : string {
        // echo "<br> DevController - in 'justDoIt'";
        self::gatherData();
        // echo "<br>  MarinController $this->route['headertemplate']  " . $this->route['headertemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['contenttemplate']  " . $this->route['contenttemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['footertemplate']  " . $this->route['footertemplate'] . "<br/>";

        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}
 