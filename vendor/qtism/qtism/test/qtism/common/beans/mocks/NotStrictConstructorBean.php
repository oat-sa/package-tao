<?php

class NotStrictConstructorBean {
    
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
     * The parameter names should be the same as the property
     * names. $hairColor has no related bean property. 
     * 
     * @param string $firstName
     * @param string $lastName
     * @param string $hairColor
     */
    public function __construct($firstName, $lastName, $hairColor) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setHair($hairColor);
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
}