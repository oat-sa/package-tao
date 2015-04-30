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
* Copyright (c) (original work) 2015 Open Assessment Technologies SA
*
*/

namespace oat\generis\test\model\kernel\persistence\file;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\model\kernel\persistence\file\FileRdf;

class FileRdfTest extends GenerisPhpUnitTestRunner
{
    /**
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
    }
    
    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Not implemented
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGet()
    {
        $rdf = new FileRdf('test');
        $rdf->get(null, null);
    }
    
    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Not implemented
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testAdd()
    {
        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';
        
        $rdf = new FileRdf('test');
        $rdf->add($triple);
    }
    
    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Not implemented
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testRemove()
    {
        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';
        
        $rdf = new FileRdf('test');
        $rdf->remove($triple);
    }
    

    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Not implemented
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSearch()
    {
        $rdf = new FileRdf('test');
        $rdf->search(null,null);
    }
    
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetIterator()
    {
        $dir = GenerisPhpUnitTestRunner::getSampleDir();
        $rdf = new FileRdf($dir.'/rdf/sample.rdf');
        $this->assertInstanceOf('oat\generis\model\kernel\persistence\file\FileIterator', $rdf->getIterator());
    }
}

?>