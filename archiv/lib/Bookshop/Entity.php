<?php 

namespace Bookshop; 

interface IData {
    // typisierte Rückgabewerte nur in PHP 7
    public function getId() : int; 
}

class Entity extends BaseObject implements IData {
    private $id; 

    /**
     * @param int $id
     */
    public function __construct(int $id) {
        $this->id = intval($id);
    }
    
    public function getId(): int {
        return $this->id; 
    }
}
