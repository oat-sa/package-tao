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
use oat\tao\model\messaging\MessagingService;
use oat\tao\model\messaging\Message;
use oat\tao\model\messaging\transportStrategy\FileSink;
use Prophecy\Prediction\CallTimesPrediction;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package tao
 */
class MessagingServiceTest extends TaoPhpUnitTestRunner
{
    /**
     * 
     * @param Transport $transport
     * @return MessagingService
     */
    protected function getMessagingService($transport) {
        $messagingService = MessagingService::singleton();
        $refObject = new ReflectionObject($messagingService);
        $refProperty = $refObject->getProperty('transport');
        $refProperty->setAccessible( true );
        $refProperty->setValue($messagingService, $transport);
        return $messagingService;
    }

    public function testSend()
    {
        $message = new Message();
        $transportProphecy = $this->prophesize('oat\tao\model\messaging\Transport');
        $transportProphecy->send($message)->willReturn(true);
        $transportProphecy->send($message)->should(new CallTimesPrediction(1));
        
        $messagingService = $this->getMessagingService($transportProphecy->reveal());
        
        $result = $messagingService->send($message);
        
        $transportProphecy->checkProphecyMethodsPredictions();
        $this->assertTrue($result);
    }
    
    public function testIsAvailable()
    {
        $transportMock = $this->getMock('oat\tao\model\messaging\Transport');
        $messagingService = $this->getMessagingService($transportMock);
        
        $this->assertTrue($messagingService->isAvailable());
    }
}
