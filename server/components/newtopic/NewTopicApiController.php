<?php

class NewTopicApiController extends SimpleController {

    protected function gatherData() {
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
        $this->data = $ret; 
    }

    public function justDoIt() : string {
        $this->gatherData();

        // if no logged in -> early exit
        if (!$this->data["isloggedin"])  {
            return json_encode( $this->data );
        }
        
        $title = $this->route["requestparameter"]["title"];
        $description = $this->route["requestparameter"]["description"];
        $channelid = $this->route["requestparameter"]["channelid"];
        $userid = $this->data["userid"];

        \Logger::logDebug("NewTopicApiController::justDoIt() values in route 'channelid' $channelid ", "");
        \Logger::logDebug("NewTopicApiController::justDoIt() values in route 'title' $title ", "");
        \Logger::logDebug("NewTopicApiController::justDoIt() values in route 'description' $description ", "");

        $ret = null; 
        if (\DataBaseManager::insertTopic($userid, $channelid, $title, $description)) {
            \Logger::logDebug("NewTopicApiController::justDoIt() insert success  ", "");

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

        \Logger::logDebugPrintR("NewTopicApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
