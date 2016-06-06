<?php
use oat\generis\model\user\PasswordConstraintsException;
use oat\generis\model\user\PasswordConstraintsService;

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

    /**
     * @param $password
     *
     * @return string
     * @throws PasswordConstraintsException
     */
    public function encrypt($password) {

        if ( PasswordConstraintsService::singleton()->validate($password)){
            $salt = helpers_Random::generateString($this->saltLength);
            return $salt.hash($this->algorithm, $salt.$password);
        }

        throw new PasswordConstraintsException(
            __( 'Password must be: %s' ,
            implode( ',', PasswordConstraintsService::singleton()->getErrors() )
        ));
    }

    public function verify($password, $hash) {
        $salt = substr($hash, 0, $this->saltLength);
        $hashed = substr($hash, $this->saltLength);
        return hash($this->algorithm, $salt.$password) === $hashed;
    }

}