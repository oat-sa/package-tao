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
namespace oat\generis\test\model\persistence\smoothsql;

use \core_kernel_persistence_smoothsql_SmoothRdf;
use oat\generis\test\GenerisPhpUnitTestRunner;
use Prophecy\Prophet;

class SmoothRdfTest extends GenerisPhpUnitTestRunner
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
        $prophet = new Prophet();
        $persistence = $prophet->prophesize('\common_persistence_SqlPersistence');
        
        $model = $prophet->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());
        
        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());
        $rdf->get(null, null);
    }

    /**
     * @expectedException common_Exception
     * @expectedExceptionMessage Not implemented
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSearch()
    {
        $prophet = new Prophet();
        $persistence = $prophet->prophesize('\common_persistence_SqlPersistence');
        
        $model = $prophet->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());
        
        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());
        $rdf->search(null, null);
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testAdd()
    {
        $persistence = $this->prophesize('\common_persistence_SqlPersistence');
        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language) VALUES ( ? , ? , ? , ? , ? );";
        
        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';
        
        $persistence->exec($query, array(
            22,
            'subjectUri',
            'predicateUri',
            'objectUri',
            ''
        ))->willReturn(true);
        
        $model = $this->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getReadableModels()->willReturn(array(22));
        $model->getPersistence()->willReturn($persistence->reveal());
        
        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());
        
        $this->assertTrue($rdf->add($triple));
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testRemove()
    {
        $prophet = new Prophet();
        $persistence = $prophet->prophesize('\common_persistence_SqlPersistence');
        $query = "DELETE FROM statements WHERE subject = ? AND predicate = ? AND object = ? AND l_language = ?;";
        
        $triple = new \core_kernel_classes_Triple();
        $triple->modelid = 22;
        $triple->subject = 'subjectUri';
        $triple->predicate = 'predicateUri';
        $triple->object = 'objectUri';
        
        $persistence->exec($query, array(
            'subjectUri',
            'predicateUri',
            'objectUri',
            ''
        ))->willReturn(true);
        
        $model = $prophet->prophesize('\core_kernel_persistence_smoothsql_SmoothModel');
        $model->getPersistence()->willReturn($persistence->reveal());
        
        
        $rdf = new core_kernel_persistence_smoothsql_SmoothRdf($model->reveal());
        
        $this->assertTrue($rdf->remove($triple));
    }
}

?>