<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoTests\models\runner;

/**
 * Class SessionCsrfToken
 *
 * Handles CSRF token through the PHP session
 *
 * @package oat\taoTests\models\runner
 */
class SessionCsrfToken implements CsrfToken
{
    /**
     * The name of the storage key for the current CSRF token
     */
    const TOKEN_KEY = 'SECURITY_TOKEN_';

    /**
     * The name of the storage key for the creation time of the current CSRF token
     */
    const TIMESTAMP_KEY = 'SECURITY_TIME_';

    /**
     * The desired length of the CSRF token string
     */
    const TOKEN_LENGTH = 40;

    /**
     * The token name
     * 
     * @var string
     */
    protected $name;
    
    /**
     * SessionCsrfToken constructor.
     * @param string $name The token name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the token name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Generates and returns the CSRF token
     * @return string
     */
    public function getCsrfToken()
    {
        $session = \PHPSession::singleton();

        $token = bin2hex(openssl_random_pseudo_bytes(self::TOKEN_LENGTH / 2));
        $session->setAttribute(self::TOKEN_KEY . $this->name, $token);
        $session->setAttribute(self::TIMESTAMP_KEY . $this->name, time());
        return $token;
    }

    /**
     * Validates a given token with the current CSRF token
     * @param string $token The given token to validate
     * @param int $lifetime A max life time for the current token, default to infinite
     * @return bool
     */
    public function checkCsrfToken($token, $lifetime = 0)
    {
        $valid = true;
        $session = \PHPSession::singleton();
        
        $tokenKey = self::TOKEN_KEY . $this->name;
        $timestampKey = self::TIMESTAMP_KEY . $this->name;

        if ($lifetime && $session->hasAttribute($timestampKey)) {
            $valid = $lifetime >= time() - $session->getAttribute($timestampKey);
        }

        if ($valid) {
            $valid = !$session->hasAttribute($tokenKey) || $session->getAttribute($tokenKey) == $token;
        }

        return $valid;
    }

    /**
     * Revokes the current CSRF token
     * @return void
     */
    public function revokeCsrfToken()
    {
        $session = \PHPSession::singleton();

        $session->removeAttribute(self::TOKEN_KEY . $this->name);
        $session->removeAttribute(self::TIMESTAMP_KEY . $this->name);
    }
}
