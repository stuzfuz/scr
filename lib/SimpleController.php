<?php

class SimpleController {

    private $route = null; 
    private $data = null; 

    public function __construct($route) {
        $this->route = $route; 
    }

    protected function gatherData() {
        
        $this->data = 'dkfjs';
    }

    public function justDoIt() : string {
        echo "<br> " . $this->data . "<br/>";
        $page = \TemplateEngine::render('index.tmpl', $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}