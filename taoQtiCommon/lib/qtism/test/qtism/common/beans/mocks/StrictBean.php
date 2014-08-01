<?php

class StrictBean {
    
    /**
     * 
     * @var string
     * @qtism-bean-property
     */
    private $firstName;
    
    /**
     * 
     * @var string
     * @qtism-bean-property
     */
    private $lastName;
    
    
    /**
     * 
     * @var string
     * @qtism-bean-property
     */
    private $hair;
    
    /**
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $cool;
    
    public function __construct($firstName, $lastName, $hair, $cool) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setHair($hair);
        $this->setCool($cool);
    }
    
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    
    public function getFirstName() {
        return $this->girstName;
    }
    
    public function setLastName($lastName) {
        return $this->lastName;
    }
    
    public function getLastName() {
        return $this->lastName;
    }

    public function setHair($hair) {
        $this->hair = $hair;
    }
    
    public function getHair() {
        return $this->hair;
    }
    
    public function setCool($cool) {
        $this->cool = $cool;
    }
    
    public function isCool() {
        return $this->cool;
    }
}