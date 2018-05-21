<?php

class MessageStatusNotImportantApiController extends SimpleController {

    protected function gatherData() {
        \Logger::logDebug("MessageStatusNotImportantApiController::gatherData() BEGIN", "");

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
        \Logger::logDebug("MessageStatusNotImportantApiController::gatherData() END", "");

        $this->data = $ret; 
    }

    public function justDoIt() : string {
        \Logger::logDebug("MessageStatusNotImportantApiController::justDoIt() BEGIN", "");

        $this->gatherData();

        // if no logged in -> early exit
        if (! $this->data["isloggedin"])  {
            return json_encode( $this->data );
        }
        
        $messageid = $this->route["requestparameter"]["messageid"];
        $userid = $this->data["userid"];

        \Logger::logDebug("MessageStatusNotImportantApiController::justDoIt() values in route 'messageid' $messageid ", "");
        \Logger::logDebug("MessageStatusNotImportantApiController::justDoIt() values in route 'userid' $userid ", "");

        $ret = null; 
        if (\DataBaseManager::markMessageNotImportant($userid, $messageid)) {
            \Logger::logDebug("MessageStatusNotImportantApiController::justDoIt() insert success  ", "");

            // 200 everything is fine 
            header_remove();
            http_response_code(200);                
            $ret["status"] = "OK";
        } else {
            header_remove();
            http_response_code(401);
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: could not mark the  new message as NOT important -> please contact our support!";
        }

        \Logger::logDebugPrintR("MessageStatusNotImportantApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
