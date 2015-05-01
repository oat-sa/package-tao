<?php

/**
 * A utility class focusing on Randomization.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class helpers_PasswordHash {
    
    private $algorithm;
    private $saltLength;
    
    public function __construct($algorithm, $saltLength) {
        $this->algorithm = $algorithm;
        $this->saltLength = $saltLength;
    }

    public function encrypt($password) {
        $salt = helpers_Random::generateString($this->saltLength);
        return $salt.hash($this->algorithm, $salt.$password);
    }
    
    public function verify($password, $hash) {
        $salt = substr($hash, 0, $this->saltLength);
        $hashed = substr($hash, $this->saltLength);
        return hash($this->algorithm, $salt.$password) == $hashed;
    }
}