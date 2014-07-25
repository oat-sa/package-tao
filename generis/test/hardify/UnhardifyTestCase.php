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
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class UnhardifyTestCase extends UnitTestCase {

	public function setUp(){
		TaoTestRunner::initTest();
	}

	public function testHardify(){
		 
		ob_start(); // catch the output and drop it
		try {
			$wfEngineHardifier = new wfEngine_scripts_HardifyWfEngine (array(
					'min'		=> 1,
					'required'	=> array(
							array('compile'),
							array('decompile')
					),
					'parameters' => array(
							array(
									'name' 			=> 'compile',
									'type' 			=> 'boolean',
									'shortcut'		=> 'c',
									'required'		=> true,
									'description'	=> 'Compile the workflow triple store to relational database'
							),
							array(
									'name' 			=> 'decompile',
									'type' 			=> 'boolean',
									'shortcut'		=> 'd',
									'required'		=> true,
									'description'	=> 'Get the data from the workflow relational database to the triple store (if previously compiled)'
							),
							array(
									'name'			=> 'indexes',
									'type' 			=> 'boolean',
									'shortcut'		=> 'i',
									'description'	=> 'Create extra indexes on compiled tables and rebuild exisiting indexes databases'
							)
					)
			), array ('argv'=>array('-d -i', '-d', '-i')));
		}
		catch (Exception $e){
			var_dump($e);
		}

		ob_end_clean();
		set_time_limit(900); // because the script update the time limit
	}
}

?>