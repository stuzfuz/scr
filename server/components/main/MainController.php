<?php

class MainController extends SimpleController {

    protected function gatherData() {
        $ret = null; 
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $ret["username"] = '@' . $user->getUserName();
            $ret["loggedin"] = true;
        } else {
            $ret["loggedin"] = false;
        }
        $this->data = $ret; 
        // \Util::my_var_dump($this->data, "MainController this->data  = ");
    }

    // // TODO Delete this if not necessary
    public function justDoIt() : string {
        // echo "<br> MainControiller - in 'justDoIt'";
        self::gatherData();
        // echo "<br>  MarinController $this->route['headertemplate']  " . $this->route['headertemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['contenttemplate']  " . $this->route['contenttemplate'] . "<br/>";
        // echo "<br>  MarinController $this->route['footertemplate']  " . $this->route['footertemplate'] . "<br/>";

        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}
 