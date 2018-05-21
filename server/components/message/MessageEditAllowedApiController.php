<?php

class MessageEditAllowedApiController extends SimpleController {

    protected function gatherData() {
        \Logger::logDebug("MessageEditAllowedApiController::gatherData() BEGIN", "");

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
        \Logger::logDebug("MessageEditAllowedApiController::gatherData() END", "");

        $this->data = $ret; 
    }

    public function justDoIt() : string {
        \Logger::logDebug("MessageEditAllowedApiController::justDoIt() BEGIN", "");

        $this->gatherData();

        // if no logged in -> early exit
        if (! $this->data["isloggedin"])  {
            return json_encode( $this->data );
        }
        
        $messageid = $this->route["requestparameter"]["messageid"];
        $userid = $this->data["userid"];

        \Logger::logDebug("MessageEditAllowedApiController::justDoIt() values in route 'messageid' $messageid ", "");
        \Logger::logDebug("MessageEditAllowedApiController::justDoIt() values in route 'userid' $userid ", "");

        $ret = null; 
        if (\DataBaseManager::messageUnread($messageid)) {
            \Logger::logDebug("MessageEditAllowedApiController::justDoIt() message can be edited  ", "");

            // 200 everything is fine 
            header_remove();
            http_response_code(200);                
            $ret["status"] = "OK";
        } else {
            header_remove();
            http_response_code(401);
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: message can not be edited!";
        }

        \Logger::logDebugPrintR("MessageEditAllowedApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
