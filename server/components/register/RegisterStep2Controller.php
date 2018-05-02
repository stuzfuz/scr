<?php

class RegisterStep2Controller extends SimpleController {

    protected function gatherData() {
        if (!isset($this->route["requestparameter"]["username"]) || (!isset($this->route["requestparameter"]["password"])) ||
            (!isset($this->route["requestparameter"]["firstname"]) || (!isset($this->route["requestparameter"]["lastname"])))) {
            \Logger::logDebugPrintR("'RegisterStep2Controller::gatherData()' [" . __LINE__ ."]  no routeparam 'username' or 'password' or'firstname' or 'lastname' provided ", $this->route["requestparameter"]); 
            \Util::quit500("Fatal Error - 'RegisterStep2Controller::gatherData()' [" . __LINE__ ."] no routeparam provided   ", $this->route["requestparameter"]);
        }

        $userid = \DatabaseManager::insertUser($this->route["requestparameter"]["username"],
            $this->route["requestparameter"]["password"],
            $this->route["requestparameter"]["firstname"],
            $this->route["requestparameter"]["lastname"]);


        $ret["stepone"] = false;
        $ret["userid"] = $userid;
        $this->data = $ret; 
    }

    public function justDoIt() : string {
        self::gatherData();
        die("step333333");
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
    
}
 