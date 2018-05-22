<?php

class NewChannelApiController extends SimpleController {

    protected function gatherData() {
        $ret = null; 
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $ret["userid"] =  $user->getId();
            $ret["username"] = '@' . $user->getUserName();
            $ret["isloggedin"] = true;
        } else {
            \Util::redirect("/");
        }
        $this->data = $ret; 
    }

    public function justDoIt() : string {
        $this->gatherData();

        \Logger::logDebugPrintR("LoginApiController::justDoIt() trying to authenicate user  route = ", $this->route);
        
        $channelname = $this->route["requestparameter"]["channelname"];
        $description = $this->route["requestparameter"]["description"];
        \Logger::logDebug("LoginApiController::justDoIt() values in route 'channelname' $channelname ", "");
        \Logger::logDebug("LoginApiController::justDoIt() values in route 'description' $description ", "");

        $ret = null; 
        if (\DataBaseManager::existsChannel($channelname)) {
            \Logger::logDebug("LoginApiController::justDoIt() channel '$channelname' already exists  ", "");

            // 401 not authorized is the "best" fit
            header_remove();
            http_response_code(401);
            $ret["status"] = "Error";
            $ret["errorcode"] = 3;
            $ret["status"] = "ERROR: channelname already exists -> please choose a different name!";            
        } else {
            \Logger::logDebug("LoginApiController::justDoIt() inserting channel into list of channels", "");
            if (\DataBaseManager::insertChannel($this->data["userid"], $channelname, $description)) {
                // 200 everything is fine 
                header_remove();
                http_response_code(200);
                $ret["status"] = "OK";
            } else {
                header_remove();
                http_response_code(401);
                $ret["status"] = "Error";
                $ret["errorcode"] = 3;
                $ret["status"] = "ERROR: could not insert the new channel -> please contact our support!";
            }
        }

        \Logger::logDebugPrintR("LoginApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
