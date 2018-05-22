<?php

class LoginApiController extends SimpleController {
    public function justDoIt() : string {
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

            // 401 not authorized is the "best" fit
            header_remove();
            http_response_code(401);
            $ret["status"] = "ERROR";
        }

        // https://gist.github.com/james2doyle/33794328675a6c88edd6
        // header_remove();
        // http_response_code(404);


        \Logger::logDebugPrintR("LoginApiController json_encode( ret ) = ", json_encode( $ret ));
        
        return json_encode( $ret );
    }    
}
 