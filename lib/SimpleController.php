<?php

class SimpleController {

    protected $route = null; 
    protected $data = null; 

    public function __construct($route) {
        $this->route = $route; 
    }

    protected function gatherData() {
        // query data from db and session
        // this will be implemented by each controller
    }

    public function justDoIt() : string {
        $page = \TemplateEngine::render('index.tmpl', $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}