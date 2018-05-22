<?php

class RegisterStep2PostController extends SimpleController {

    protected function gatherData() {
    }

    public function justDoIt() : string {
        if (!isset($this->route["requestparameter"]["username"]) || (!isset($this->route["requestparameter"]["password"])) ||
            (!isset($this->route["requestparameter"]["firstname"]) || (!isset($this->route["requestparameter"]["lastname"])))) {
            \Logger::logDebugPrintR("'RegisterStep2PostController::gatherData()'  [" . __LINE__ ."]  POST   no routeparam 'username' or 'password' or'firstname' or 'lastname' provided ", $this->route["requestparameter"]); 
            \Util::quit500("Fatal Error - 'RegisterStep2PostController::gatherData()' [" . __LINE__ ."] POST   no routeparam provided   ", $this->route["requestparameter"]);
        }

        // insert user into db and return the new userid
        $userid = \DatabaseManager::insertUser($this->route["requestparameter"]["username"],
            $this->route["requestparameter"]["password"],                
            $this->route["requestparameter"]["firstname"],
            $this->route["requestparameter"]["lastname"]);
        
        if ($userid == null) {
            header_remove();
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: an Error occured in the database -> please contact our support";
            return json_encode($ret); 
        }
 
        // automatic login the new user
        $res = \AuthenticationManager::authenticate($this->route["requestparameter"]["username"], $this->route["requestparameter"]["password"]);
        \Logger::logDebug("'RegisterStep2PostController::gatherData()' [" . __LINE__ ."]   result of authenticate() ", $res); 

        // die();
        
        if ($res) {
            \Logger::logDebug("'RegisterStep2PostController::gatherData()' [" . __LINE__ ."]   LOGIN of user was succesful ",""); 
            // die("user logged in succesfully");
        } else {
            \Logger::logDebug("'RegisterStep2PostController::gatherData()' [" . __LINE__ ."]   LOGIN of user was NOOOT  succesful ","");
            header_remove();
            $ret["status"] = "Error";
            $ret["errorcode"] = 4;
            $ret["status"] = "ERROR: an Error occured -> please contact our support";
            return json_encode($ret); 
        }

        header_remove();
        http_response_code(200);
        $ret["status"] = "OK: user inserted and logged in ";
        \Logger::logDebugPrintR("'RegisterStep2PostController::gatherData()' [" . __LINE__ ."]   POST    ret   ", $ret); 

        return json_encode($ret); 
    }
}
