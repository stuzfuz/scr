<?php

class MessageDeleteApiController extends SimpleController {

    protected function gatherData() {
        \Logger::logDebug("MessageDeleteApiController::gatherData() BEGIN", "");

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
        \Logger::logDebug("MessageDeleteApiController::gatherData() END", "");

        $this->data = $ret; 
    }

    public function justDoIt() : string {
        \Logger::logDebug("MessageDeleteApiController::justDoIt() BEGIN", "");

        $this->gatherData();

        // if no logged in -> early exit
        if (! $this->data["isloggedin"])  {
            return json_encode( $this->data );
        }
        
        $messageid = $this->route["requestparameter"]["messageid"];
        $userid = $this->data["userid"];

        \Logger::logDebug("MessageDeleteApiController::justDoIt() values in route 'messageid' $messageid ", "");
        \Logger::logDebug("MessageDeleteApiController::justDoIt() values in route 'userid' $userid ", "");

        $ret = null; 
        // pass the userid to the method -> only the author of the message can delete the message!
        if (\DataBaseManager::markMessageDeleted($userid, $messageid)) {
            \Logger::logDebug("MessageDeleteApiController::justDoIt() insert success  ", "");

            // 200 everything is fine 
            header_remove();
            http_response_code(200);                
            $ret["status"] = "OK";
        } else {
            header_remove();
            http_response_code(401);
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: could not delete  message!";
        }

        \Logger::logDebugPrintR("MessageDeleteApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
