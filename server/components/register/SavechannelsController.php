<?php

class SavechannelsController extends SimpleController {

    protected function gatherData() {
    }

    public function justDoIt() : string {
        if (!isset($this->route["requestparameter"]["channels"])) {
            \Logger::logDebugPrintR("'SavechannelsController::gatherData()'  [" . __LINE__ ."]  POST   no channels to add for this user - fine by me ","");
            // \Util::quit500("Fatal Error - 'SavechannelsController::gatherData()' [" . __LINE__ ."] POST   no routeparam 'channels' provided  ", $this->route["requestparameter"]);
            
            header_remove();
            http_response_code(200);
            $ret["status"] = "OK: user did not select any channels";
            \Logger::logDebugPrintR("'RegisterStep2PostController::gatherData()' [" . __LINE__ ."]   POST    this->data   ", $this->data); 
            
            return json_encode($ret);
        } 

        // user should be logged in, but who knows (timeout session)

        //TODO redirect to /register again? what about the already save username?
        if (!\AuthenticationManager::isAuthenticated()) {
            // die("in RegisterStep2GetController - user is NOTNOTNOT logged in:-( ");
            \Logger::logDebugPrintR("'SavechannelsController::gatherData()' [" . __LINE__ ."]  user should be logged in -> cant continue :-(  ", ""); 
            \Util::quit500("SavechannelsController::gatherData()   [" . __LINE__ ."] user should be logged at this point of time  -> cant continue :-(   ", "");   
            // \Util::redirect("/");
        }

        // user is logged in and we have some channels the user wants to join ...
        $user = \AuthenticationManager::getAuthenticatedUser();

        \Logger::logDebugPrintR("'SavechannelsController::gatherData()' [" . __LINE__ ."]  channels from request  ", $this->route["requestparameter"]["channels"]); 

        // if (is_array($this->route["requestparameter"]["channels"])) {
        //     die("is an array");
        // } else {
        //     die("is not an array");
        // }
        // insert user into db and return the new userid
        $res = \DatabaseManager::assignUserChannelsTopicsMessages($user->getId(), $this->route["requestparameter"]["channels"]);
        
        if (!$res) {
            \Logger::logDebugPrintR("'SavechannelsController::gatherData()' [" . __LINE__ ."]  could not save channels for user  :-(  ", ""); 

            header_remove();
            http_response_code(401);
            $ret["errorcode"] = 5;
            $ret["status"] = "ERROR: an Error occured saving the channels -> please contact our support";
            return json_encode($ret); 
        }
        
        header_remove();
        http_response_code(200);
        $ret["status"] = "OK: user assigned to channels ";
        \Logger::logDebugPrintR("'RegisterStep2PostController::gatherData()' [" . __LINE__ ."]   POST    this->data   ", $this->data); 

        return json_encode($ret);
    }
}
