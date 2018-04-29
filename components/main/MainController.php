<?php

class MainController  extends SimpleController {

    protected function gatherData() {
        $this->data["loggedin"] = false;
    }

    // TODO Delete this if not necessary
    public function justDoIt() : string {
        echo "<br> " . $this->data . "<br/>";
        $page = \TemplateEngine::render(\ApplicationConfig::$indexTmpl, $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return "MainController";
    }
}
 