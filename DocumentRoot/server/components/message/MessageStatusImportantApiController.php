<?php

class MessageStatusImportantApiController extends SimpleController {

    protected function gatherData() {
        \Logger::logDebug("MessageStatusImportantApiController::gatherData() BEGIN", "");

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
        \Logger::logDebug("MessageStatusImportantApiController::gatherData() END", "");

        \Logger::logAccess($user->getId(), "mark message as important");

        $this->data = $ret; 
    }

    public function justDoIt() : string {
        \Logger::logDebug("MessageStatusImportantApiController::justDoIt() BEGIN", "");

        $this->gatherData();

        // if no logged in -> early exit
        if (! $this->data["isloggedin"])  {
            return json_encode( $this->data );
        }
        
        $messageid = $this->route["requestparameter"]["messageid"];
        $userid = $this->data["userid"];

        \Logger::logDebug("MessageStatusImportantApiController::justDoIt() values in route 'messageid' $messageid ", "");
        \Logger::logDebug("MessageStatusImportantApiController::justDoIt() values in route 'userid' $userid ", "");

        $ret = null; 
        if (\DataBaseManager::markMessageImportant($userid, $messageid)) {
            \Logger::logDebug("MessageStatusImportantApiController::justDoIt() insert success  ", "");

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

        \Logger::logDebugPrintR("MessageStatusImportantApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
