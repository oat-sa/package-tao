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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 */
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\tao\model\passwordRecovery\PasswordRecoveryService;
use oat\tao\model\messaging\transportStrategy\FileSink;
use Prophecy\Prediction\CallTimesPrediction;
use Prophecy\Argument;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package tao
 */
class PasswordRecoveryServiceTest extends TaoPhpUnitTestRunner
{
    /**
     * @var core_kernel_classes_Resource
     */
    protected $testUser = null;

    /**
     * tests initialization
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->testUser = $this->createUser();
    }

    /**
     * tests clean up
     */
    public function tearDown()
    {
        if (!is_null($this->testUser)) {
            $this->testUser->delete();
        }
    }
    
    /**
     * 
     * @param MessagingService $messagingService
     * @return PasswordRecoveryService
     */
    protected function getPasswordRecoveryService($messagingService) {
        $passwordRecoveryService = PasswordRecoveryService::singleton();
        $refObject = new ReflectionObject($passwordRecoveryService);
        $refProperty = $refObject->getProperty('messagingSerivce');
        $refProperty->setAccessible( true );
        $refProperty->setValue($passwordRecoveryService, $messagingService);
        return $passwordRecoveryService;
    }
    
    protected function createUser() {
        $class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        return $class->createInstanceWithProperties(array(
            PROPERTY_USER_LOGIN => 'john.doe',
            PROPERTY_USER_PASSWORD => core_kernel_users_Service::getPasswordHash()->encrypt('secure'),
            PROPERTY_USER_LASTNAME => 'Doe',
            PROPERTY_USER_FIRSTNAME => 'John',
            PROPERTY_USER_MAIL => 'jonhdoe@tao.lu',
            PROPERTY_USER_DEFLG => 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
            PROPERTY_USER_UILG => 'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
            PROPERTY_USER_ROLES => INSTANCE_ROLE_BACKOFFICE
        ));
    }

    public function testSendMail()
    {
        $messagingProphecy = $this->prophesize('oat\tao\model\messaging\MessagingService');
        $messagingProphecy->isAvailable()->willReturn(true);
        $messagingProphecy->isAvailable()->should(new CallTimesPrediction(1));
        $user = $this->testUser;
        $messagingProphecy->send(Argument::type('oat\tao\model\messaging\Message'))->will(function ($args) use ($user) {
            $message = $args[0];
            $tokenProperty = new core_kernel_classes_Property(PasswordRecoveryService::PROPERTY_PASSWORD_RECOVERY_TOKEN);
            $token = (string)$user->getOnePropertyValue($tokenProperty);
            if (is_null($token) || strpos($message->getBody(), $token) == false) {
                throw new Exception('Token not found in body');
            }
            return true;
        });
        $messagingProphecy->send(Argument::type('oat\tao\model\messaging\Message'))->should(new CallTimesPrediction(1));
        
        
        $passwordRecoveryService = $this->getPasswordRecoveryService($messagingProphecy->reveal());
        
        $generisUser = new \core_kernel_users_GenerisUser($this->testUser);
        $this->assertEmpty($generisUser->getPropertyValues(PasswordRecoveryService::PROPERTY_PASSWORD_RECOVERY_TOKEN));
        $generisUser->refresh();
        
        $this->assertTrue($passwordRecoveryService->sendMail($this->testUser));
        
        $passwordRecoveryToken = current($generisUser->getPropertyValues(PasswordRecoveryService::PROPERTY_PASSWORD_RECOVERY_TOKEN));
        $this->assertNotEmpty($passwordRecoveryToken);
        
        $messagingProphecy->checkProphecyMethodsPredictions();
    }
}
