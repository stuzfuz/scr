<?php

class LoginApiController extends SimpleController {

    protected function gatherData() {
        \Logger::logDebugPrintR("MessagesController this->data  = ", $this->data);
    }

    public function justDoIt() : string {
        // self::gatherData();

        \Logger::logDebugPrintR("LoginApiController::justDoIt() trying to authenicate user  route = ", $this->route);
        
        $username = $this->route["requestparameter"]["username"];
        $password = $this->route["requestparameter"]["password"];
        \Logger::logDebug("LoginApiController::justDoIt() values in route 'username' $username ", "");
        \Logger::logDebug("LoginApiController::justDoIt() values in route 'password' $password ", "");
        

        $ret = null; 
        if (\AuthenticationManager::authenticate($username, $password)) {
            \Logger::logDebug("LoginApiController::justDoIt() authenitcation successful'  ", "");

            header_remove();
            http_response_code(200);
            $ret["status"] = "OK";
        } else {
            \Logger::logDebug("LoginApiController::justDoIt() authenitcation NOTTTT  successful'  ", "");

            header_remove();
            http_response_code(200);
            $ret["status"] = "ERROR";
        }

        // https://gist.github.com/james2doyle/33794328675a6c88edd6
        // header_remove();
        // http_response_code(404);

        // \Logger::logDebugPrintR("MessagesController this->data  = ", $this->data);
        
        return json_encode( $ret );
    }    
}
 