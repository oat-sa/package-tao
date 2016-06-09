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

namespace oat\taoTests\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTests\models\runner\SessionCsrfToken;

class SessionCsrfTokenTest extends TaoPhpUnitTestRunner
{
    /**
     * tests initialization
     */
    public function setUp()
    {
        parent::setUp();
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * The token handler must provide the given name
     */
    public function testGetNameTest()
    {
        $tokenName = 'test';
        $csrfToken = new SessionCsrfToken($tokenName);

        // the token must have the given name
        $this->assertEquals($tokenName, $csrfToken->getName());
    }

    /**
     * The token handler must generate tokens
     */
    public function testGetCsrfToken()
    {
        $tokenName = 'test';
        $csrfToken = new SessionCsrfToken($tokenName);

        // the token must be different on each get
        $this->assertNotEquals($csrfToken->getCsrfToken(), $csrfToken->getCsrfToken());

        // the token is stored in session
        $this->assertTrue(isset($_SESSION[SESSION_NAMESPACE][SessionCsrfToken::TOKEN_KEY . $tokenName]));
        $this->assertTrue(isset($_SESSION[SESSION_NAMESPACE][SessionCsrfToken::TIMESTAMP_KEY . $tokenName]));

        // the token in session must be equal to the got token
        $token = $csrfToken->getCsrfToken();
        $this->assertEquals($token, $_SESSION[SESSION_NAMESPACE][SessionCsrfToken::TOKEN_KEY . $tokenName]);

        // the token must have the right length
        $this->assertEquals(SessionCsrfToken::TOKEN_LENGTH, strlen($csrfToken->getCsrfToken()));
    }

    /**
     * The token handler must validate tokens
     */
    public function testCheckCsrfToken()
    {
        $tokenName = 'test';
        $csrfToken = new SessionCsrfToken($tokenName);

        $token = $csrfToken->getCsrfToken();

        // the token must be valid
        $this->assertTrue($csrfToken->checkCsrfToken($token));

        // the token must not be valid after a new generate
        $csrfToken->getCsrfToken();
        $this->assertFalse($csrfToken->checkCsrfToken($token));

        $lifeTime = 1000;
        $token = $csrfToken->getCsrfToken();

        // the token must be valid when the timeout is not reached
        $this->assertTrue($csrfToken->checkCsrfToken($token, $lifeTime));

        // the token is stored in session
        $this->assertTrue(isset($_SESSION[SESSION_NAMESPACE][SessionCsrfToken::TOKEN_KEY . $tokenName]));
        $this->assertTrue(isset($_SESSION[SESSION_NAMESPACE][SessionCsrfToken::TIMESTAMP_KEY . $tokenName]));

        // the token must be valid when the time is over
        $_SESSION[SESSION_NAMESPACE][SessionCsrfToken::TIMESTAMP_KEY . $tokenName] -= $lifeTime + 10;
        $this->assertFalse($csrfToken->checkCsrfToken($token, $lifeTime));
    }

    /**
     * The token handler must allow to revoke tokens
     */
    public function revokeCsrfToken()
    {
        $tokenName = 'test';
        $csrfToken = new SessionCsrfToken($tokenName);

        $token = $csrfToken->getCsrfToken();

        // the token must be valid
        $this->assertTrue($csrfToken->checkCsrfToken($token));

        // the token must be ignored
        $this->revokeCsrfToken();
        $this->assertTrue($csrfToken->checkCsrfToken($token));

        // the token must not be valid after a new generate
        $csrfToken->getCsrfToken();
        $this->assertFalse($csrfToken->checkCsrfToken($token));
    }
}