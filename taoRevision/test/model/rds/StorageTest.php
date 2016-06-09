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

namespace oat\taoRevision\test\model\rds;


use oat\taoRevision\model\storage\RdsStorage;
use oat\taoRevision\model\Revision;
use oat\oatbox\service\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use oat\taoRevision\scripts\install\CreateTables;

class StorageTest extends \PHPUnit_Framework_TestCase {


    private $storage = null;

    public function setUp(){
      
        $persistenceManager = new \common_persistence_Manager(array(
            'persistences' => array(
                'persistenceMock' => array(
                    'driver' => 'dbal',
                    'connection' => array(
                        'driver' => 'pdo_sqlite',
                        'memory' => true
                    )
                )
            )
        ));
        
        $serviceManager = $this->getMockForAbstractClass(ServiceLocatorInterface::class);
        $serviceManager->method('get')
            ->with(\common_persistence_Manager::SERVICE_KEY)
            ->willReturn($persistenceManager);

        $createTables = new CreateTables();
        $createTables->setServiceLocator($serviceManager);
        $createTables(array('persistenceMock'));
        
        $this->storage = new RdsStorage(array('persistence' => 'persistenceMock'));
        $this->storage->setServiceLocator($serviceManager);

    }

    public function tearDown(){

        $this->storage = null;
    }


    public function testAddGetRevision(){
        
        // set the revision we are supposed to get
        $resourceId = 123;
        $version = 456;
        $author = "author";
        $message = "my message";
        $created = time();
        $data = array();

        $revision = new Revision($resourceId, $version, $created, $author, $message);

        $persist = $this->storage->getServiceLocator()->get(\common_persistence_Manager::SERVICE_KEY)->getPersistenceById('persistenceMock');
        $count = $persist->query('select count(*) as count from revision')->fetch()['count'];
        
        $this->assertEquals(0, $count);
        $returnValue = $this->storage->addRevision($resourceId, $version, $created, $author, $message, array());
        $count = $persist->query('select count(*) as count from revision')->fetch()['count'];
        $this->assertEquals(1, $count);
        $returnValue = $this->storage->getRevision($resourceId, $version);
        
        $this->assertEquals($revision, $returnValue);
        
        
        return $revision;
    }

    public function testGetAllRevisions() {

        // set revisions we are supposed to get
        $resourceId = 123;
        $version1 = 456;
        $version2 = 789;
        $author = "author";
        $message1 = "my message";
        $message2 = "my new message";
        $created1 = time();
        $created2 = time();

        $revisions = array(
            $this->storage->addRevision($resourceId, $version1, $created1, $author, $message1, array()),
            $this->storage->addRevision($resourceId, $version2, $created2, $author, $message2, array())
        );
        
        $returnValue = $this->storage->getAllRevisions($resourceId);

        $this->assertEquals($revisions, $returnValue);

    }


    public function testGetData() {

        // set the revision we are supposed to get
        $resourceId = 123;
        $version = 456;
        $author = "author";
        $message = "my message";
        $created = time();
        $revision = new Revision($resourceId, $version, $created, $author, $message);

        $subject1 = "my first subject";
        $predicate1 = "my first predicate";
        $object1 = "my first object";
        $lg1 = "en-en";

        $subject2 = "my second subject";
        $predicate2 = "my second predicate";
        $object2 = "my second object";
        $lg2 = "fr-fr";

        // Initialize the expected values
        $triple1 = new \core_kernel_classes_Triple();
        $triple2 = new \core_kernel_classes_Triple();
        $triple1->modelid = 1;
        $triple1->subject = $subject1;
        $triple1->predicate = $predicate1;
        $triple1->object = $object1;
        $triple1->lg = $lg1;
        $triple2->modelid = 1;
        $triple2->subject = $subject2;
        $triple2->predicate = $predicate2;
        $triple2->object = $object2;
        $triple2->lg = $lg2;
        $triples = array($triple1, $triple2);
        
        $revision = $this->storage->addRevision($resourceId, $version, $created, $author, $message, $triples);
        
        
        
        $data = $this->storage->getData($revision);

        $this->assertEquals($triples, $data);
    }


}
 