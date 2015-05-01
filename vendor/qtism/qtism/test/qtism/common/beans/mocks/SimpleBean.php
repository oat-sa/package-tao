<?php

class SimpleBean {
    
    /**
     * The name of the SimpleBean object.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $name;
    
    /**
     * The car of the SimpleBean object.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $car;
    
    /**
     * A useless property for testing purpose.
     * 
     * @var string
     */
    private $uselessProperty;
    
    /**
     * Another useless property because its getter is private.
     * 
     * @var string
     */
    private $anotherUselessProperty;
    
    public function __construct($name, $car, $uselessProperty = '') {
        $this->setName($name);
        $this->setCar($car);
        $this->setUselessProperty($uselessProperty);
    }  

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setCar($car) {
        $this->car = $car;
    }
    
    public function getCar() {
        return $this->car;
    }
    
    public function setUselessProperty($uselessProperty) {
        $this->uselessProperty = $uselessProperty;
    }
    
    public function getUselessProperty() {
        return $this->uselessProperty;
    }
    
    private function setAnotherUselessProperty($anotherUselessProperty) {
        $this->anotherUselessProperty = $anotherUselessProperty;
    }
    
    public function getAnotherUselessProperty() {
        return $this->anotherUselessProperty;
    }
}