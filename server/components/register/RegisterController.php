<?php

class RegisterController extends SimpleController {

    protected function gatherData() {
        $ret["stepone"] = true;
        $this->data = $ret; 
    }

    public function justDoIt() : string {
        self::gatherData();
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }    
}
