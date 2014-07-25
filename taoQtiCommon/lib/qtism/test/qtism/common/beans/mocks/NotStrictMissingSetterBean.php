<?php

class NotStrictMissingSetterBean {
    
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
    
    public function __construct($firstName, $lastName, $hair) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setHair($hair);
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
    
    /**
     * The setHair method should be public.
     * 
     * @param string $hair
     */
    protected function setHair($hair) {
        $this->hair = $hair;
    }
    
    public function getHair() {
        return $this->hair;
    }
}