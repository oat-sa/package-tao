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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * The ModelCreator enables you to import Ontologies into a TAO module
 *
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 * @package tao
 
 *
 */
class tao_install_utils_ModelCreator{

	/**
	 * @var string the module namesapce
	 */
	protected $localNs = '';

	/**
	 * Instantiate a creator for a module
	 * @param string $localNamespace
	 */
	public function __construct($localNamespace){
		if(empty($localNamespace) || !preg_match("/^http/", $localNamespace)){
			throw new tao_install_utils_Exception("$localNamespace is not valid namespace URI for the local namespace!");
		}
		$this->localNs = $localNamespace;
		if(!preg_match("/#$/", $this->localNs)){
			$this->localNs .= '#';
		}
	}

	/**
	 * Specifiq method to insert the super user model,
	 * by using a template RDF file
	 * @param array $userData
	 */
	public function insertSuperUser(array $userData){

		if (empty($userData['login']) || empty($userData['password'])){
			throw new tao_install_utils_Exception("To create a super user you must provide at least a login and a password");
		}

		$superUserOntology = dirname(__FILE__) . "/../ontology/superuser.rdf";

		if (!@is_readable($superUserOntology)){
			throw new tao_install_utils_Exception("Unable to load ontology : ${superUserOntology}");
		}

		$doc = new DOMDocument();
		$doc->load($superUserOntology);

		foreach ($userData as $key => $value){
			$tags = $doc->getElementsByTagNameNS('http://www.tao.lu/Ontologies/generis.rdf#', $key);
			foreach ($tags as $tag){
				$tag->appendChild($doc->createCDATASection($value));
			}
		}
		return $this->insertLocalModel($doc->saveXML());
	}
	
	public function insertGenerisUser($login){
		
		$generisUserOntology = dirname(__FILE__) . '/../ontology/generisuser.rdf';
		
		if (!@is_readable($generisUserOntology)){
			throw new tao_install_utils_Exception("Unable to load ontology : ${generisUserOntology}");
		}
		
		$doc = new DOMDocument();
		$doc->load($generisUserOntology);
		
		return $this->insertLocalModel($doc->saveXML(), array('{SYS_USER_LOGIN}'	=> $login));
	}

	/**
	 * Insert a model into the local namespace
	 * @throws tao_install_utils_Exception
	 * @param string $file the path to the RDF file
	 * @return boolean true if inserted
	 */
	public function insertLocalModelFile($file){
		if(!file_exists($file) || !is_readable($file)){
			throw new tao_install_utils_Exception("Unable to load ontology : $file");
		}
		return $this->insertLocalModel(file_get_contents($file));
	}

	/**
	 * Insert a model into the local namespace
	 * @param string $model the XML data
	 * @return boolean true if inserted
	 */
	public function insertLocalModel($model, $replacements = array()){
		$model = str_replace('LOCAL_NAMESPACE#', $this->localNs, $model);
		$model = str_replace('{ROOT_PATH}', ROOT_PATH, $model);
		
		foreach ($replacements as $k => $r){
			$model = str_replace($k, $r, $model);
		}

		return $this->insertModel($this->localNs, $model);
	}

	/**
	 * Insert a model
	 * @throws tao_install_utils_Exception
	 * @param string $file the path to the RDF file
	 * @return boolean true if inserted
	 */
	public function insertModelFile($namespace, $file){
		if(!file_exists($file) || !is_readable($file)){
			throw new tao_install_utils_Exception("Unable to load ontology : $file");
		}
		return $this->insertModel($namespace, file_get_contents($file));
	}

	/**
	 * Insert a model
	 * @param string $model the XML data
	 * @return boolean true if inserted
	 */
	public function insertModel($namespace, $model){

		$returnValue = false;
		if(!preg_match("/#$/", $namespace)){
			$namespace .= '#';
		}

		
        $modFactory = new core_kernel_api_ModelFactory();
        $returnValue = $modFactory->createModel($namespace, $model);
               

        return $returnValue;
	}

    /**
     * Convenience method that returns available language descriptions to be inserted in the
     * knowledge base.
     * 
     * @return array of ns => files
     */
    public function getLanguageModels() {
        $models = array();
        $ns = $this->localNs;
        
        $extensionPath = dirname(__FILE__) . '/../../../tao';
        $localesPath = $extensionPath . '/locales';
        
        if (@is_dir($localesPath) && @is_readable($localesPath)) {
            $localeDirectories = scandir($localesPath);
            
            foreach ($localeDirectories as $localeDir) {
                $path = $localesPath . '/' . $localeDir;
                if ($localeDir[0] != '.' && @is_dir($path)){
                    // Look if the lang.rdf can be read.
                    $languageModelFile = $path . '/lang.rdf';
                    if (@file_exists($languageModelFile) && @is_readable($languageModelFile)){
                        // Add this file to the returned values.
                        if (!isset($models[$ns])){
                            $models[$ns] = array();
                        }
                        
                        $models[$ns][] = $languageModelFile;
                    }
                }
            }
        
            return $models;
        }
        else{
            throw new tao_install_utils_Exception("Cannot read 'locales' directory in extenstion 'tao'.");
        }
    }
}
?>