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
 *
 */

namespace oat\taoRevision\test\model;


use oat\taoRevision\model\Revision;
use oat\taoRevision\model\RepositoryService;
use Zend\ServiceManager\ServiceLocatorInterface;
use oat\taoRevision\model\Repository;
use Prophecy\Promise\ReturnPromise;
use oat\taoRevision\model\RevisionStorage;

function time()
{
    return RepositoryTest::$now ?: \time();
}


class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public static $now;
    
    private function getRepository($storage)
    {
        $smProphecy = $this->prophesize(ServiceLocatorInterface::class);
        $smProphecy->get('mockStorage')->willReturn($storage);

        $repository = new RepositoryService(array(RepositoryService::OPTION_STORAGE => 'mockStorage'));
        $repository->setServiceLocator($smProphecy->reveal());
        return $repository;
    }
    
    public function tearDown(){
        self::$now = null;
    }


    public function testGetRevisions(){

        $returnValue = array(456, 789);
        $resourceId = 123;

        $storageProphecy = $this->prophesize(RevisionStorage::class);
        $storageProphecy->getAllRevisions($resourceId)->willReturn($returnValue);
        
        $repository = $this->getRepository($storageProphecy->reveal());

        $return = $repository->getRevisions($resourceId);
        $this->assertEquals($returnValue, $return);
        
        $storageProphecy->getAllRevisions($resourceId)->shouldHaveBeenCalled();
    }

    public function testGetRevision(){

        $resourceId = "MyId";
        $version = "version";
        $created = time();
        $author = "author";
        $message = "my author";
        $revision = new Revision($resourceId, $version, $created, $author, $message);

        $storageProphecy = $this->prophesize(RevisionStorage::class);
        $storageProphecy->getRevision($resourceId, $version)->willReturn($revision);
        $repository = $this->getRepository($storageProphecy->reveal());
        
        $return = $repository->getRevision($resourceId, $version);

        $this->assertEquals($revision, $return);
        $storageProphecy->getRevision($resourceId, $version)->shouldHaveBeenCalled();
    }


    public function testCommit(){

        $resourceId = "MyId";
        $version = "version";
        self::$now = time();
        $author = "author";
        $message = "my message";
        $revision = new Revision($resourceId, $version, self::$now, $author, $message);

        $storageProphecy = $this->prophesize(RevisionStorage::class);
        $storageProphecy->addRevision($resourceId, $version, self::$now, $author, $message, array())->willReturn($revision);
        
        $repository = $this->getRepository($storageProphecy->reveal());
        
        //mock user manager to get custom identifier
        $user = $this->getMockBuilder('oat\oatbox\user\User')
            ->disableOriginalConstructor()
            ->getMock();

        $user->expects($this->once())
            ->method('getIdentifier')
            ->willReturn("author");

        $session = $this->getMockBuilder('common_session_Session')
            ->disableOriginalConstructor()
            ->getMock();

        $session->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $ref = new \ReflectionProperty('common_session_SessionManager', 'session');
        $ref->setAccessible(true);
        $ref->setValue(null, $session);

        $return = $repository->commit($resourceId, $message, $version);
        $this->assertEquals($revision, $return);

    }

    public function testRestore()
    {
        $resourceId = "MyId";
        $version = "version";
        self::$now = time();
        $author = "author";
        $message = "my message";
        $revision = new Revision($resourceId, $version, self::$now, $author, $message);
        $data = array();
        
        $storageProphecy = $this->prophesize(RevisionStorage::class);
        $storageProphecy->getData($revision)->willReturn($data);
        
        $repository = $this->getRepository($storageProphecy->reveal());

        $return = $repository->restore($revision);

        $this->assertEquals(true, $return);
    }
}
