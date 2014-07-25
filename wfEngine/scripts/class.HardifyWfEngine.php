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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/scripts/class.HardifyWfEngine.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.05.2011, 12:13:09 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once(dirname(__FILE__).'/../../tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-includes begin
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-includes end

/* user defined constants */
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-constants begin
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-constants end

/**
 * Short description of class wfEngine_scripts_HardifyWfEngine
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage scripts
 */
class wfEngine_scripts_HardifyWfEngine
extends tao_scripts_Runner
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute MODE_SMOOTH2HARD
	 *
	 * @access public
	 * @var int
	 */
	const MODE_SMOOTH2HARD = 1;

	/**
	 * Short description of attribute MODE_HARD2SMOOTH
	 *
	 * @access public
	 * @var int
	 */
	const MODE_HARD2SMOOTH = 2;

	/**
	 * Short description of attribute mode
	 *
	 * @access protected
	 * @var int
	 */
	protected $mode = 0;

	// --- OPERATIONS ---

	/**
	 * Short description of method preRun
	 *
	 * @access protected
	 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
	 * @return mixed
	 */
	protected function preRun()
	{
		// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD6 begin

		if(!empty($this->parameters['compile'])){
			$this->mode = self::MODE_SMOOTH2HARD;
		}
		if(!empty($this->parameters['decompile'])){
			$this->mode = self::MODE_HARD2SMOOTH;
		}
		 
		// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD6 end
	}

	/**
	 * Short description of method run
	 *
	 * @access protected
	 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
	 * @return mixed
	 */
	protected function run()
	{
		// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD4 begin

		if (!defined('DEBUG_PERSISTENCE')){
			define ('DEBUG_PERSISTENCE', false);
		}
		 
		switch($this->mode){
			case self::MODE_SMOOTH2HARD:
				 
				$this->out("Compiling triples to relational database...", array('color' => 'light_blue'));
				 
				$options = array(
    				'recursive'				=> true,
    				'append'				=> true,
					'createForeigns'		=> false,
					'referencesAllTypes'	=> true,
					'rmSources'				=> true
				);
				 
				$switcher = new core_kernel_persistence_Switcher(array('http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessVariables',
																	   'http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable'));
				 
				$api = core_kernel_impl_ApiModelOO::singleton();
				$toCompile = $api->getAllClasses()->toArray();
				
				foreach ($toCompile as $tC){
					$classLabel = $tC->getLabel();
					$this->out("\nCompiling '${classLabel}' class...", array('color' => 'light_blue'));
					$switcher->hardify($tC, $options);
				}
				
				unset($switcher);
				
				$this->out("Compilation process complete.");
				 
				break;

				 
			case self::MODE_HARD2SMOOTH:
				 
				$this->out("Uncompiling relational database to triples...", array('color' => 'light_blue'));
				 
				$options = array(
    				'recursive'				=> true,
    				'removeForeigns'		=> true				
				);
				 
				$switcher = new core_kernel_persistence_Switcher(array('http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessVariables',
																	   'http://www.tao.lu/Ontologies/TAOResult.rdf#GradeVariable'));
				 
				$api = core_kernel_impl_ApiModelOO::singleton();
				$toDecompile = $api->getAllClasses()->toArray();
				
				foreach ($toDecompile as $tD){
					$classLabel = $tD->getLabel();
					$this->out("\nDecompiling '${classLabel}' class...", array('color' => 'light_blue'));
					$switcher->unhardify($tD, $options);
				}
				
				$this->out("Decompilation process complete.");
				
				unset($switcher);
				 
				break;

			default:
				$this->err('Unknow mode', true);
		}
		 
		// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD4 end
	}

	/**
	 * Short description of method postRun
	 *
	 * @access protected
	 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
	 * @return mixed
	 */
	protected function postRun()
	{
		// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD8 begin

		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$referencer->clearCaches();
		 
		if(isset($this->parameters['indexes']) && $this->parameters['indexes'] == true){

			$dbWrapper = core_kernel_classes_DbWrapper::singleton();

			//Create indexes on discrimining columns
			$this->out("\nCreate extra indexes, it can take a while...");

			//uris of the indexes to add to single columns
			$indexProperties = array(
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersFormalParameter',
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertySupportServicesUrl',
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsType',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsActivityReference',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesStatus',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesExecutionOf',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsStatus',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser',
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsProcessExecution',
		    	'http://www.tao.lu/Ontologies/generis.rdf#login',
		    	'http://www.tao.lu/Ontologies/generis.rdf#password',
		    	'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_PROCESS_EXEC_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#AO_DELIVERY_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_TEST_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_ITEM_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_SUBJECT_ID'
    		);

    		foreach($indexProperties as $indexProperty){
    			$property = new core_kernel_classes_Property($indexProperty);
    			$propertyAlias = core_kernel_persistence_hardapi_Utils::getShortName($property);
    			foreach($referencer->propertyLocation($property) as $table){
    				if(!preg_match("/Props$/", $table) && preg_match("/^_[0-9]{2,}/", $table)){
    					try{
    						$dbWrapper->createIndex("idx_${propertyAlias}", $table, array($propertyAlias => 255));
    					}
    					catch (PDOException $e){
    						if ($e->getCode() != $dbWrapper->getIndexAlreadyExistsErrorCode() && $e->getCode() != '00000'){
    							throw new Exception("Unable to create index for table '${table}'");
    						}
    					}
    				}
    			}
    		}

    		$this->out("\nRebuild table indexes, it can take a while...");

    		//Need to OPTIMIZE / FLUSH the tables in order to rebuild the indexes
    		$tables = $dbWrapper->getTables();

    		$size = count($tables);
    		$i = 0;
    		while($i < $size){
    			 
    			$percent = round(($i / $size) * 100);
    			if($percent < 10){
    				$percent = '0'.$percent;
    			}
    			$this->out(" ${percent} %", array('color' => 'light_green', 'inline' => true, 'prefix' => "\r"));
    			 
    			$dbWrapper->rebuildIndexes($tables[$i]);
    			$dbWrapper->flush($tables[$i]);
    			 
    			$i++;
    		}
		}
		 
		$this->out("\nFinished !\n", array('color' => 'light_blue'));
		// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD8 end
	}

} /* end of class wfEngine_scripts_HardifyWfEngine */

?>