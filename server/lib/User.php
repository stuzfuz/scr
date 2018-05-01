<?php

/**
 * User
 * 
 * 
 * @extends Entity
 * @package    
 * @subpackage 
 * @author     John Doe <jd@fbi.gov>
 */

class User {

  private $id;
  private $userName;
  private $password;

  public function __construct(int $id, string $userName, string $password) {
    $this->userName = $userName;
    $this->id = $id;
    $this->password = $password;
  }

  /**
   * getter for the private parameter $userName
   *
   * @return string
   */
  public function getUserName() : string {
    return $this->userName;
  }

  /**
   * getter for the private parameter $password
   *
   * @return string
   */
  public function getPassword() : string {
    return $this->password;
  }

  public function getId() : string {
    return $this->id;
  }
}

