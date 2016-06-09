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

use oat\taoRevision\model\Revision;
class RevisionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Revision
     */
    private $revision = null;
    
    private $time;

    public function setUp(){
        $this->time = time();
        
        $resourceId = 123;
        $version = 456;
        $created = $this->time;
        $author = "Great author";
        $message = "My message is really cool";
        $this->revision = new Revision($resourceId, $version, $created, $author, $message);
    }

    public function tearDown(){
        $this->revision = null;
    }

    public function testConstruct(){
        $this->assertInstanceOf("oat\\taoRevision\\model\\Revision",$this->revision, "RdsRevision should extends Revision");

    }

    public function testGetters(){

        $this->assertEquals(123, $this->revision->getResourceId());
        $this->assertEquals(456, $this->revision->getVersion());
        $this->assertEquals($this->time, $this->revision->getDateCreated());
        $this->assertEquals("Great author", $this->revision->getAuthorId());
        $this->assertEquals("My message is really cool", $this->revision->getMessage());

    }

}
 