<?php

class MessageEditApiController extends SimpleController {

    protected function gatherData() {
        \Logger::logDebug("MessageEditApiController::gatherData() BEGIN", "");

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
        \Logger::logDebug("MessageEditApiController::gatherData() END", "");

        \Logger::logAccess($user->getId(), "edit message");

        $this->data = $ret; 
    }

    public function justDoIt() : string {
        \Logger::logDebug("MessageEditApiController::justDoIt() BEGIN", "");

        $this->gatherData();

        // if no logged in -> early exit
        if (! $this->data["isloggedin"])  {
            return json_encode( $this->data );
        }
        
        $messageid = $this->route["requestparameter"]["messageid"];
        $userid = $this->data["userid"];
        $txt = $this->route["requestparameter"]["txt"];
        
        \Logger::logDebug("MessageEditApiController::justDoIt() values in route 'messageid' $messageid ", "");
        \Logger::logDebug("MessageEditApiController::justDoIt() values in route 'userid' $userid ", "");

        $ret = null; 
        if (\DataBaseManager::updateMessage($userid, $messageid, $txt)) {
            \Logger::logDebug("MessageEditApiController::justDoIt() insert success  ", "");

            // 200 everything is fine 
            header_remove();
            http_response_code(200);                
            $ret["status"] = "OK";
        } else {
            header_remove();
            http_response_code(401);
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: could not mark the  new message as important -> please contact our support!";
        }

        \Logger::logDebugPrintR("MessageEditApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
