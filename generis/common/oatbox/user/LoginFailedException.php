<?php
namespace oat\oatbox\user;

use common_user_auth_AuthFailedException;

/**
 * Exception indicating that all authentication attempts failed
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class LoginFailedException extends common_user_auth_AuthFailedException
{
    private $exceptions;
    
    public function __construct(array $exceptions) {
        $this->exceptions = $exceptions;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_exception_UserReadableException::getUserMessage()
     */
    public function getUserMessage() {
        if (count($this->exceptions) == 1) {
            $e = reset($this->exceptions);
            return $e->getUserMessage();
        } else {
            return __('Invalid login or password. Please try again.');
        }
    }
}
