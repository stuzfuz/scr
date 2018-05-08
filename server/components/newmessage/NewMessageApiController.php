<?php

class NewMessageApiController extends SimpleController {

    protected function gatherData() {
        \Logger::logDebug("NewMessageApiController::gatherData() BEGIN", "");

        $ret = null; 
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $ret["userid"] =  $user->getId();
            $ret["username"] = '@' . $user->getUserName();
            $ret["isloggedin"] = true;
        } else {
            http_response_code(401);
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: your are not logged in - please login and try again!";
            $ret["isloggedin"] = false;
        }
        \Logger::logDebug("NewMessageApiController::gatherData() END", "");

        $this->data = $ret; 
    }

    public function justDoIt() : string {
        \Logger::logDebug("NewMessageApiController::justDoIt() BEGIN", "");

        $this->gatherData();

        // if no logged in -> early exit
        if (! $this->data["isloggedin"])  {
            return json_encode( $this->data );
        }
        
        $txt = $this->route["requestparameter"]["txt"];
        $topicid = $this->route["requestparameter"]["topicid"];
        $userid = $this->data["userid"];

        \Logger::logDebug("NewMessageApiController::justDoIt() values in route 'txt' $txt ", "");
        \Logger::logDebug("NewMessageApiController::justDoIt() values in route 'topicid' $topicid ", "");

        $ret = null; 
        if (\DataBaseManager::insertMessage($userid, $topicid, $txt)) {
            \Logger::logDebug("NewMessageApiController::justDoIt() insert success  ", "");

            // 200 everything is fine 
            header_remove();
            http_response_code(200);                
            $ret["status"] = "OK";
        } else {
            header_remove();
            http_response_code(401);
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: could not insert the new message -> please contact our support!";
        }

        \Logger::logDebugPrintR("NewMessageApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
