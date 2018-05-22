<?php

class NewChannelController extends SimpleController {

    protected function gatherData() {
        $ret = null; 
        if (\AuthenticationManager::isAuthenticated()) {
            $user = \AuthenticationManager::getAuthenticatedUser();
            $ret["username"] = '@' . $user->getUserName();
            $ret["isloggedin"] = true;
        } else {
            \Util::redirect("/");
        }
        \Logger::logAccess($user->getId(), "new channel");
        
        $this->data = $ret; 
    }

    public function justDoIt() : string {
        self::gatherData();
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }    
}
