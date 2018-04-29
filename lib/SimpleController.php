<?php

class SimpleController {

    protected $db_conn = null; 
    protected $route = null; 
    protected $data = null; 

    public function __construct($db_conn, $route) {
        $this->route = $route; 
        $this->db_conn = $db_conn;
    }

    protected function gatherData() {
        // echo "<br> simple controller - in 'gather data'";
        // query data from db and session
        // this will be implemented by each controller
    }

    public function justDoIt() : string {
        // echo "<br> simple controiller - call gather data";
        self::gatherData();
        $page = \TemplateEngine::render('index.tmpl', $this->data, $this->route['headertemplate'], $this->route['contenttemplate'], $this->route['footertemplate']);
        return $page;
    }
}