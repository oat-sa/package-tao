<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013      (update and modification) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';

/**
 * Test of the DbWrappers.
 * 
 * @author Jerome Bogaerts <jerome.bogaerts@tudor.lu>
 * @package generis
 * @subpackage test
 */
class DbWrapperTestCase extends UnitTestCase {

    public function setUp(){
        GenerisTestRunner::initTest();
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        //TODO need to connect to a dbWrapper a function createTable that currently not exists
        $dbWrapper->exec('
                CREATE TABLE "dbTestCase" (
                    "id" INT,
                    "uri" VARCHAR(255) NOT NULL,
                    "column1"VARCHAR(255) )'
        );
        for($i = 0;$i<4;$i++){
            $dbWrapper->exec('INSERT INTO  "dbTestCase" (id,uri,column1) VALUES (?,?,?) ;',array($i,'http://uri'.$i,'value'.$i));
        }
	}


    public function testGetRowCount(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$rowCount = $dbWrapper->getRowCount('dbTestCase');
		$this->assertTrue(is_int($rowCount));
		$this->assertTrue($rowCount == 4);
        $dbWrapper->exec('INSERT INTO "dbTestCase" (id,uri,column1) VALUES (?,?,?) ;',array('12','http://uri','value'));
        $rowCount = $dbWrapper->getRowCount('dbTestCase');
        $this->assertTrue(is_int($rowCount));
        $this->assertTrue($rowCount == 5);
	}

	public function testGetColumnNames(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$columns = $dbWrapper->getColumnNames('dbTestCase');
        $this->assertTrue(count($columns) == 3);
        $possibleValues = array('id','uri','column1');
        $this->assertTrue(in_array($columns[0],$possibleValues));
        $this->assertTrue(in_array($columns[1],$possibleValues));
        $this->assertTrue(in_array($columns[2],$possibleValues));

	}

    public function testGetTables(){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $tables = $dbWrapper->getTables();
        $this->assertTrue(count($tables) == 9);
        $this->assertTrue(in_array('class_additional_properties', $tables));
        $this->assertTrue(in_array('class_to_table', $tables));
        $this->assertTrue(in_array('dbTestCase', $tables));
        $this->assertTrue(in_array('extensions', $tables));
        $this->assertTrue(in_array('models', $tables));
        $this->assertTrue(in_array('resource_has_class', $tables));
        $this->assertTrue(in_array('resource_to_table', $tables));
        $this->assertTrue(in_array('sequence_uri_provider', $tables));
        $this->assertTrue(in_array('statements', $tables));
    }

    public function testLimitStatements(){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $query = 'SELECT * from "dbTestCase" ';
        $results = $dbWrapper->query($query);
        $count = $results->rowCount();
        $this->assertTrue($count==4);

        // limit to 2 offset from 1
        $query2 = $dbWrapper->limitStatement($query, 2, 1);
        $results2 = $dbWrapper->query($query2);
        $count2 = $results2->rowCount();

        $this->assertTrue($count2==2);
        $i =0;
        foreach($results2 as $value){
            // let's go to offset 1
            $i++;
            $this->assertTrue(is_array($value));
            $this->assertTrue($value['id'] == $i);
            $this->assertTrue($value['uri'] == 'http://uri'.$i);
            $this->assertTrue($value['column1'] == 'value'.$i);
        }



    }

    public function tearDown(){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        //TODO need to connect to a dbWrapper a function dropTable that currently not exists
        $dbWrapper->exec('DROP TABLE "dbTestCase";');
    }
}

?>