<?php

class RegisterController extends SimpleController {

    protected function gatherData() {
        $ret = null; 
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            \Logger::logAccess($user->getId(), "register -> redirectet to /");
            \Util::redirect("/");
        } 
        \Logger::logAccess(-1, "register");

        $ret["stepone"] = true;
        $this->data = $ret; 
    }

    public function justDoIt() : string {
        self::gatherData();
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }    
}
