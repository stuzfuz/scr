<?php

namespace BookShop;

class Category extends Entity {
    private $name; 

    public function __construct(int $id, string $name) {
        parent::__construct($id);
        $this->name = $name; 
    }

    public function getName():  string {
        return $this->name; 
    }
}
