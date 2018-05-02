<?php

class CheckusernameController extends SimpleController {

    protected function gatherData() {
    }

    public function justDoIt() : string {
        if (!isset($this->route["requestparameter"]["username"])) {
            \Logger::logDebugPrintR("CheckusernameController::justDoIt() no parameter username set in POST request body route['requestparameter'] ", $this->route["requestparameter"]);
            header_remove();
            http_response_code(401);
            $ret["errorcode"] = 1;
            $ret["status"] = "ERROR: no username specified in request body";
            return json_encode( $ret );
        }
        $username = $this->route["requestparameter"]["username"];
        \Logger::logDebug("CheckusernameController::justDoIt() value in route '/api/checkusername' $username ", "");
        
        $ret = null; 

        $sql = "SELECT username FROM user WHERE username = ?";
        $res = \DatabaseManager::query($this->db_conn, $sql, array($username));
        
        if ($res->rowCount() == 0) {
            \Logger::logDebugPrintR("'CheckusernameController::gatherData()' [" . __LINE__ ."]  username not found  =   ", $username);
            header_remove();
            http_response_code(200);
            $ret["status"] = "OK: username available";
        } else {
            header_remove();
            http_response_code(401);
            $ret["errorcode"] = 2;
            $ret["status"] = "ERROR: username already taken";
        }
        return json_encode( $ret );
    }
}
 