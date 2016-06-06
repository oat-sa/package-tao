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


use oat\taoRevision\model\rds\RdsRevision;

class RdsRevisionTest extends \PHPUnit_Framework_TestCase {

    private $rdsRevision = null;
    private $id = null;

    public function setUp(){
        $this->id = 'myFunId';
        $resourceId = 123;
        $version = 456;
        $created = time();
        $author = "Great author";
        $message = "My message is really cool";
        $this->rdsRevision = new RdsRevision($this->id, $resourceId, $version, $created, $author, $message);
    }

    public function tearDown(){
        $this->id = null;
        $this->rdsRevision = null;
    }

    public function testConstruct(){
        $this->assertInstanceOf("oat\\taoRevision\\model\\Revision",$this->rdsRevision, "RdsRevision should extends Revision");

    }

    public function testGetId(){

        $this->assertEquals($this->id, $this->rdsRevision->getId(), "The collected id is wrong");

    }

}
 