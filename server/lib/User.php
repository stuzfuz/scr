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
  private $username;
  private $password;

  public function __construct(int $id, string $username, string $password) {
    $this->username = $username;
    $this->id = $id;
    $this->password = $password;
  }

  /**
   * getter for the private parameter $username
   *
   * @return string
   */
  public function getusername() : string {
    return $this->username;
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

