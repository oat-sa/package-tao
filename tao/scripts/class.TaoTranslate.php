<?php
use oat\tao\model\menu\MenuService;
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * The TaoTranslate script aims at providing command line tools to manage
 * of tao. It enables you to manage the i18n of the messages found in the source
 * (gettext) but also i18n of RDF Models.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_scripts_TaoTranslate
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute DEF_INPUT_DIR
     *
     * @access public
     * @var string
     */
    const DEF_INPUT_DIR = '.';

    /**
     * Short description of attribute DEF_OUTPUT_DIR
     *
     * @access public
     * @var string
     */
    const DEF_OUTPUT_DIR = 'locales';

    /**
     * Short description of attribute DEF_PO_FILENAME
     *
     * @access public
     * @var string
     */
    const DEF_PO_FILENAME = 'messages.po';

    /**
     * Short description of attribute DEF_JS_FILENAME
     *
     * @access public
     * @var string
     */
    const DEF_JS_FILENAME = 'messages_po.js';

    /**
     * Short description of attribute options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Short description of attribute DEF_LANG_FILENAME
     *
     * @access public
     * @var string
     */
    const DEF_LANG_FILENAME = 'lang.rdf';

    /**
     * Short description of attribute DEF_PHP_FILENAME
     *
     * @access public
     * @var string
     */
    const DEF_PHP_FILENAME = 'messages.lang.php';

    private static $WHITE_LIST = array(
        'actions',
        'helpers',
        'models',
        'views',
        'helper',
        'controller',
        'model'
    );

    protected $verbose = false;
    // --- OPERATIONS ---


    /**
     * keys - action names from user input
     * value - base name for method to call
     * @return array
     */
    protected function getAllowedActions(){
        return array(
            'create'=>'Create',
            'update'=>'Update',
            'delete'=>'Delete',
            'updateall'=>'UpdateAll',
            'deleteall'=>'DeleteAll',
            'enable'=>'Enable',
            'disable'=>'Disable',
            'compile'=>'Compile',
            'compileall'=>'CompileAll',
            'changecode'=>'ChangeCode',
            'getallextensions'=>'GetExt',
        );
    }

    /**
     * Things that must happen before script execution.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function preRun()
    {
        
    	$this->options = array('verbose' => false,
        					   'action' => null,
    						   'extension' => null);
        
        $this->options = array_merge($this->options, $this->parameters);
        
        if ($this->options['verbose'] == true) {
        	$this->verbose = true;
        }
        
        // The 'action' parameter is always required.
        if ($this->options['action'] == null) {
        	$this->err("Please enter the 'action' parameter.", true);
        } else {
        	$this->options['action'] = strtolower($this->options['action']);
        	if (!in_array($this->options['action'], array_keys($this->getAllowedActions()))) {
        		$this->err("'" . $this->options['action'] . "' is not a valid action.", true);
        	} else {
        		// The 'action' parameter is ok.
        		// Let's check additional inputs depending on the value of the 'action' parameter.
        		$this->checkInput();	
        	}
        }
        
    }

    /**
     * Main script implementation.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function run()
    {
        // Select the action to perform depending on the 'action' parameter.
        // Verification of the value of 'action' performed in self::preRun().
        $actions = $this->getAllowedActions();
        $pendingActionName = 'action' . $actions[$this->options['action']];
        if (method_exists($this, $pendingActionName)) {
            return $this->$pendingActionName();
        }
    }

    /**
     * Things that must happen after the run() method.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function postRun()
    {
        
        
    }

    /**
     * Checks the inputs for the current script call.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkInput()
    {
        $actions = $this->getAllowedActions();
        $pendingActionName = 'check' . $actions[$this->options['action']].'Input';
        if (method_exists($this, $pendingActionName)) {
            return $this->$pendingActionName();
        }
    }

    /**
     * Checks the inputs for the 'create' action.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkCreateInput()
    {
        
        $defaults = array('language' => null,
                          'languageLabel' => null,
        				  'extension' => null,
        				  'input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
        				  'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR,
        				  'build' => true, // Build translation files by having a look in source code, models.
        				  'force' => false); // Do not force rebuild if locale already exist.
        
        $this->options = array_merge($defaults, $this->options);
    	
    	if (is_null($this->options['language'])) {
        	$this->err("Please provide a 'language' identifier such as en-US, fr-CA, IT, ...", true);
        } else {
        	if (is_null($this->options['extension'])) {
        		$this->err("Please provide an 'extension' for which the 'language' will be created", true);
        	} else {
        		// Check if the extension(s) exists.
        		$extensionsToCreate = explode(',', $this->options['extension']);
        		$extensionsToCreate = array_unique($extensionsToCreate);
        		foreach ($extensionsToCreate as $etc){
        			$this->options['input'] = dirname(__FILE__) . '/../../' . $etc . '/' . self::DEF_INPUT_DIR;
        			$this->options['output'] = dirname(__FILE__) . '/../../' . $etc . '/' . self::DEF_OUTPUT_DIR;
        			$extensionDir = dirname(__FILE__) . '/../../' . $etc;
        			if (!is_dir($extensionDir)) {
        				$this->err("The extension '" . $etc . "' does not exist.", true);
        			} else if (!is_readable($extensionDir)) {
        				$this->err("The '" . $etc . "' directory is not readable. Please check permissions on this directory.", true);
        			} else if (!is_writable($extensionDir)) {
        				$this->err("The '" . $etc . "' directory is not writable. Please check permissions on this directory.", true);
        			}
        			
        			// The input 'parameter' is optional.
        			// (and only used if the 'build' parameter is set to true)
					$this->checkInputOption();

					// The 'output' parameter is optional.
					$this->checkOutputOption();
				}
        	}
        }
        
    }

    /**
     * Checks the inputs for the 'update' action.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkUpdateInput()
    {
        
        $defaults = array('language' => null,
        				  'extension' => null,
        				  'input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
        				  'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR);
        
        $this->options = array_merge($defaults, $this->options);
        
        if (is_null($this->options['language'])) {
        	$this->err("Please provide a 'language' identifier such as en-US, fr-CA, IT, ...", true);
        } else {
        	// Check if the language folder exists and is readable/writable.
        	$languageDir = $this->buildLanguagePath($this->options['extension'], $this->options['language']);
        	if (!is_dir($languageDir)) {
        		$this->err("The 'language' directory ${languageDir} does not exist.", true);
        	} else if (!is_readable($languageDir)) {
        		$this->err("The 'language' directory ${languageDir} is not readable. Please check permissions on this directory.");	
        	} else if (!is_writable($languageDir)) {
        		$this->err("The 'language' directory ${languageDir} is not writable. Please check permissions on this directory.");	
        	} else {
	        	if (is_null($this->options['extension'])) {
	        		$this->err("Please provide an 'extension' for which the 'language' will be created", true);
	        	} else {
	        		// Check if the extension exists.
	        		$extensionDir = dirname(__FILE__) . '/../../' . $this->options['extension'];
	        		if (!is_dir($extensionDir)) {
	        			$this->err("The extension '" . $this->options['extension'] . "' does not exist.", true);
	        		} else if (!is_readable($extensionDir)) {
	        			$this->err("The '" . $this->options['extension'] . "' directory is not readable. Please check permissions on this directory.", true);
	        		} else if (!is_writable($extensionDir)) {
	        			$this->err("The '" . $this->options['extension'] . "' directory is not writable. Please check permissions on this directory.", true);
	        		} else {
	        			
	        			// And can we read the messages.po file ?
	        			if (!file_exists($languageDir . '/' . self::DEF_PO_FILENAME)) {
	        				$this->err("Cannot find " . self::DEF_PO_FILENAME . " for extension '" . $this->options['extension'] . "' and language '" . $this->options['language'] . "'.", true);	
	        			} else if (!is_readable($languageDir . '/' . self::DEF_PO_FILENAME)) {
	        				$this->err(self::DEF_PO_FILENAME . " is not readable for '" . $this->options['extension'] . "' and language '" . $this->options['language'] . "'. Please check permissions for this file." , true);
	        			} else {
		        			// The input 'parameter' is optional.
							$this->checkInputOption();

							// The 'output' parameter is optional.
							$this->checkOutputOption();
	        			}
	        		}
	        	}
        	}
        	
        	
        }
        
    }

    /**
     * checks the input for the 'updateAll' action.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkUpdateAllInput()
    {
        
        $defaults = array('input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
        				  'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR,
        				  'extension' => null);
        
        $this->options = array_merge($defaults, $this->options);
        
    	// The input 'parameter' is optional.
		$this->checkInputOption();

		// The 'output' parameter is optional.
		$this->checkOutputOption();
        
    }

    /**
     * Checks the inputs for the 'delete' action.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void/
     */
    private function checkDeleteInput()
    {
        
        $defaults = array('language' => null,
        				  'input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
        				  'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR,
                          'extension' => null);
        
        $this->options = array_merge($defaults, $this->options);
        
        if (is_null($this->options['extension'])){
        	$this->err("Please provide an 'extension' identifier.", true);
        }else{
            if (is_null($this->options['language'])) {
                $this->err("Please provide a 'language' identifier such as en-US, fr-CA, IT, ...", true);
            } else {
				$this->checkInputOption();
			}
        }
        
    }

    /**
     * Checks inputs for the 'deleteAll' action.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkDeleteAllInput()
    {
        
     	$defaults = array('input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
     					  'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR,
                          'extension' => null);
        
        $this->options = array_merge($defaults, $this->options);
        
    	// The input 'parameter' is optional.
    	if (!is_null($this->options['extension'])){
            $this->checkInputOption();
        }else{
            $this->err("Please provide an 'extension' identifier.", true);
        }
        
    }
    
    private function checkChangeCodeInput(){
    	$defaults = array('input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
    					  'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR,
    					  'extension' => null,
    					  'language' => null,
    					  'targetLanguage' => null);
    	
    	$this->options = array_merge($defaults, $this->options);
    	
    	if (empty($this->options['language'])){
    		$this->err("Please provide a source 'language' identifier such as en-US, fr-CA, IT, ...", true);
    	}
    	else if (empty($this->options['targetLanguage'])){
    		$this->err("Please provide a 'targetLanguage' identifier such as en-US, fr-CA, IT, ...", true);
    	}
    	else if (empty($this->options['extension'])){
    		$this->err("Please provide an 'extension' identifier." ,true);
		}
    	else if (!is_readable($this->options['output'] . DIRECTORY_SEPARATOR . $this->options['language'])){
    		$this->err("The '" . $this->options['language'] . "' locale directory is not readable.", true);
    	}
    	else if (!is_writable($this->options['output'])){
    		$this->err("The locales directory of extension '" . $this->options['extension'] . "' is not writable.");
    	}
    	
    }

    /**
     * Implementation of the 'create' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionCreate()
    {
        
        $extensionsToCreate = explode(',', $this->options['extension']);
        $extensionsToCreate = array_unique($extensionsToCreate);
        
        foreach ($extensionsToCreate as $etc){
        	$this->options['extension'] = $etc;
        	$this->options['input'] = dirname(__FILE__) . '/../../' . $etc . '/' . self::DEF_INPUT_DIR;
        	$this->options['output'] = dirname(__FILE__) . '/../../' . $etc . '/' . self::DEF_OUTPUT_DIR;
        	
        	$this->outVerbose("Creating language '" . $this->options['language'] . "' for extension '" . $this->options['extension'] . "' ...");
        	 
        	// We first create the directory where locale files will go.
        	$dir = $this->buildLanguagePath($this->options['extension'], $this->options['language']);
        	$dirExists = false;
        	
        	if (file_exists($dir) && is_dir($dir) && $this->options['force'] == true) {
        		$dirExists = true;
        		$this->outVerbose("Language '" . $this->options['language'] . "' exists for extension '" . $this->options['extension'] . "'. Creation will be forced.");
        		 
        		// Clean it up.
        		foreach (scandir($dir) as $d) {
        			if ($d !== '.' && $d !== '..' && $d !== '.svn') {
        				if (!tao_helpers_File::remove($dir . '/' . $d, true)) {
        					$this->err("Unable to clean up 'language' directory '" . $dir . "'.", true);
        				}
        			}
        		}
        	} else if (file_exists($dir) && is_dir($dir) && $this->options['force'] == false) {
        		$this->err("The 'language' " . $this->options['language'] . " already exists in the file system. Use the 'force' parameter to overwrite it.", true);
        	}
        	
        	// If we are still here... it means that we have to create the language directory.
        	if (!$dirExists && !@mkdir($dir)) {
        		$this->err("Unable to create 'language' directory '" . $this->options['language'] . "'.", true);
        	} else {
        		if ($this->options['build'] == true) {
        			$sortingMethod = tao_helpers_translation_TranslationFile::SORT_ASC_I;
        			$this->outVerbose("Building language '" . $this->options['language'] . "' for extension '" . $this->options['extension'] . "' ...");
        			// Let's populate the language with raw PO files containing sources but no targets.
        			// Source code extraction.
        			$fileExtensions = array('php', 'tpl', 'js', 'ejs');
        			$filePaths = array();
        			foreach (self::$WHITE_LIST as $subFolder) {
        			    $filePaths[] = $this->options['input'] . DIRECTORY_SEPARATOR. $subFolder;
        			}
        			 
        			$sourceExtractor = new tao_helpers_translation_SourceCodeExtractor($filePaths, $fileExtensions);
        			$sourceExtractor->extract();
        	
        			$translationFile = new tao_helpers_translation_POFile();
        			$translationFile->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        			$translationFile->setTargetLanguage($this->options['language']);
        			$translationFile->addTranslationUnits($sourceExtractor->getTranslationUnits());
        			
        			$file = MenuService::getStructuresFilePath($this->options['extension']);
        			if (!is_null($file)) {
            			$structureExtractor = new tao_helpers_translation_StructureExtractor(array($file));
            			$structureExtractor->extract();
            			$translationFile->addTranslationUnits($structureExtractor->getTranslationUnits());
        			}

        			$sortedTus = $translationFile->sortBySource($sortingMethod);
        	
        			$sortedTranslationFile = new tao_helpers_translation_POFile();
        			$sortedTranslationFile->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        			$sortedTranslationFile->setTargetLanguage($this->options['language']);
        			$sortedTranslationFile->addTranslationUnits($sortedTus);
        			$this->preparePOFile($sortedTranslationFile, true);

        			$poPath = $dir . '/' . self::DEF_PO_FILENAME;
        			$writer = new tao_helpers_translation_POFileWriter($poPath,
        					$sortedTranslationFile);
        			$writer->write();
        			$this->outVerbose("PO Translation file '" . basename($poPath) . "' in '" . $this->options['language'] . "' created for extension '" . $this->options['extension'] ."'.");
        			$writer->write();

					// Writing JS files
					$jsPath = $dir . '/' . self::DEF_JS_FILENAME;
        			$writer = new tao_helpers_translation_JSFileWriter(
						 $jsPath, $sortedTranslationFile
					);
        			$writer->write(false);
					$this->outVerbose("JavaScript Translation file '" . basename($jsPath) . "' in '" . $this->options['language'] . "' created for extension '" . $this->options['extension'] ."'.");
					$writer->write();

        			// Now that PO files & JS files are created, we can create the translation models
        			// if we find RDF models to load for this extension.
        			$translatableProperties = array(RDFS_LABEL, RDFS_COMMENT);
        	
        			foreach ($this->getOntologyFiles() as $f){
       					common_Logger::d('reading rdf '.$f);
						$translationFile = $this->extractPoFileFromRDF($f, $translatableProperties);

						$writer = new tao_helpers_translation_POFileWriter($dir . '/' . $this->getOntologyPOFileName($f), $translationFile);
       					$writer->write();

       					$this->outVerbose("PO Translation file '" .  $this->getOntologyPOFileName($f) . "' in '" . $this->options['language'] . "' created for extension '" . $this->options['extension'] . "'.");
        			}
        	
        			$this->outVerbose("Language '" . $this->options['language'] . "' created for extension '" . $this->options['extension'] . "'.");
        		} else {
        			// Only build virgin files.
        			// (Like a virgin... woot !)
        			$translationFile = new tao_helpers_translation_POFile();
        			$translationFile->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        			$translationFile->setTargetLanguage($this->options['language']);
        			$this->preparePOFile($translationFile, true);

        			foreach ($this->getOntologyFiles() as $f){
                            common_Logger::d('reading rdf '.$f);
                            $translationFile = new tao_helpers_translation_POFile();
        					$translationFile->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        					$translationFile->setTargetLanguage($this->options['language']);
        					$translationFile->setExtensionId($this->options['extension']);

						$writer = new tao_helpers_translation_POFileWriter($dir . '/' . $this->getOntologyPOFileName($f), $translationFile);
                        $writer->write();
        			}
        	
        			$this->outVerbose("Language '" . $this->options['language'] . "' created for extension '" . $this->options['extension'] . "'.");
        		}
        	
        		// Create the language manifest in RDF.
        		if ($this->options['extension'] == 'tao'){
        			$langDescription = tao_helpers_translation_RDFUtils::createLanguageDescription($this->options['language'],
        					$this->options['languageLabel']);
        			$langDescription->save($dir . '/' . self::DEF_LANG_FILENAME);
        		}
        	}	
        }
        
    }

    /**
     * Implementation of the 'update' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionUpdate()
    {
        
        $this->outVerbose("Updating language '" . $this->options['language'] . "' for extension '" . $this->options['extension'] . "'...");
        $sortingMethod = tao_helpers_translation_TranslationFile::SORT_ASC_I;
        
       	// Get virgin translations from the source code and manifest.
       	$filePaths = array();
        foreach (self::$WHITE_LIST as $subFolder) {
            $filePaths[] = $this->options['input'] . DIRECTORY_SEPARATOR. $subFolder;
        }
        
       	$extensions = array('php', 'tpl', 'js', 'ejs');
       	$sourceCodeExtractor = new tao_helpers_translation_SourceCodeExtractor($filePaths, $extensions);
       	$sourceCodeExtractor->extract();
    	
       	$translationFile = new tao_helpers_translation_POFile();
        $translationFile->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        $translationFile->setTargetLanguage($this->options['language']);
       	$translationFile->addTranslationUnits($sourceCodeExtractor->getTranslationUnits());
       	
        $file = MenuService::getStructuresFilePath($this->options['extension']);
        if (!is_null($file)) {
            $structureExtractor = new tao_helpers_translation_StructureExtractor(array($file));
           	$structureExtractor->extract();
            $structureUnits = $structureExtractor->getTranslationUnits();
            $this->outVerbose(count($structureUnits) . ' units extracted from structures.xml.');
            $translationFile->addTranslationUnits($structureUnits);
        }

       	// For each TU that was recovered, have a look in an older version
       	// of the translations.
       	$oldFilePath = $this->buildLanguagePath($this->options['extension'], $this->options['language']) . '/' .self::DEF_PO_FILENAME;
       	$translationFileReader = new tao_helpers_translation_POFileReader($oldFilePath);
       	$translationFileReader->read();
       	$oldTranslationFile = $translationFileReader->getTranslationFile();
       	
       	foreach ($oldTranslationFile->getTranslationUnits() as $oldTu) {
       		if (($newTu = $translationFile->getBySource($oldTu)) !== null && $oldTu->getTarget() != '') {
       			// No duplicates in TFs so I simply add it whatever happens.
       			// If it already has the same one, it means we will update it.
       			$newTu->setTarget($oldTu->getTarget());
       		}
       	}
        
       	$sortedTranslationFile = new tao_helpers_translation_POFile();
        $sortedTranslationFile->setSourceLanguage($translationFile->getSourceLanguage());
        $sortedTranslationFile->setTargetLanguage($translationFile->getTargetLanguage());
       	$sortedTranslationFile->addTranslationUnits($translationFile->sortBySource($sortingMethod));
       	$this->preparePOFile($sortedTranslationFile, true);
       	
       	// Write the new ones.
       	$poFileWriter = new tao_helpers_translation_POFileWriter($oldFilePath, $sortedTranslationFile);
       	$poFileWriter->write();
        $this->outVerbose("PO translation file '" . basename($oldFilePath) . "' in '" . $this->options['language'] . "' updated for extension '" . $this->options['extension'] . "'.");

		$translatableProperties = array(RDFS_LABEL, RDFS_COMMENT);

		// We now deal with RDF models.
        foreach ($this->getOntologyFiles() as $f){
                // Loop on 'master' models.
				$translationFile = $this->extractPoFileFromRDF($f, $translatableProperties);

                // The slave RDF file is the translation of the ontology that we find in /extId/Locales/langCode.
                $slavePOFilePath = $this->buildLanguagePath($this->options['extension'], $this->options['language']) . '/' . $this->getOntologyPOFileName($f);
                if (file_exists($slavePOFilePath)){
                    // Read the existing RDF Translation file for this RDF model.
                    $poReader = new tao_helpers_translation_POFileReader($slavePOFilePath);
                    $poReader->read();
                    $slavePOFile = $poReader->getTranslationFile();
                    
                    // Try to update translation units found in the master PO file with
                    // targets found in the old translation of the ontology.
                    foreach ($slavePOFile->getTranslationUnits() as $oTu){
                        $translationFile->addTranslationUnit($oTu);
                    }
                    
                    // Remove Slave PO file. It will be overwritten by the modified Master PO file.
                    tao_helpers_File::remove($slavePOFilePath);
                }
                
                // Write Master PO file as the new Slave PO file.
                $rdfWriter = new tao_helpers_translation_POFileWriter($slavePOFilePath, $translationFile);
                $rdfWriter->write();
                $this->outVerbose("Translation model {$this->getOntologyPOFileName($f)}  in '" . $this->options['language'] . "' updated for extension '" . $this->options['extension'] . "'.");
        }
        
       	$this->outVerbose("Language '" . $this->options['language'] . "' updated for extension '" . $this->options['extension'] . "'.");
        
    }

    /**
     * Implementation of the 'updateAll' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionUpdateAll()
    {
        
        // Scan the locales folder for languages in the wwextension and
        // launch actionUpdate for each of them.

    	// Get the list of languages that will be updated.
		$locales = $this->getLanguageList();

		// We now identified locales to be updated.
    	$this->outVerbose("Languages '" . implode(',', $locales) . "' will be updated for extension '" . $this->options['extension'] . "'.");
    	foreach ($locales as $l) {
    		$this->options['language'] = $l;
    		$this->checkUpdateInput();
    		$this->actionUpdate();
    		
    		$this->outVerbose("");
    	}
        
    }

    /**
     * Implementation of the 'delete' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionDelete()
    {
        
        $this->outVerbose("Deleting language '" . $this->options['language'] . "' for extension '" . $this->options['extension'] . "' ...");
    	
    	$dir = $this->buildLanguagePath($this->options['extension'], $this->options['language']);
        if (!tao_helpers_File::remove($dir, true)) {
        	$this->err("Could not delete language '" . $this->options['language'] . "' for extension '" . $this->options['extension'] . "'.", true);	
        }
        
        $this->outVerbose("Language '" . $this->options['language'] . "' for extension '" . $this->options['extension'] . "' successfully deleted.");
        
    }

    /**
     * Implementation of the 'deleteAll' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionDeleteAll()
    {
        
    	// Get the list of languages that will be deleted.
    	$this->outVerbose("Deleting all languages for extension '" . $this->options['extension'] . "'...");

		$locales = $this->getLanguageList();
    	
    	foreach ($locales as $l) {
    		$this->options['language'] = $l;
    		$this->checkDeleteInput();
    		$this->actionDelete();
    		
    		$this->outVerbose("");
    	}
        
    }
    
    public function actionChangeCode(){
    	$this->outVerbose("Changing code of locale '" . $this->options['language'] . "' to '" . $this->options['targetLanguage'] . "' for extension '" . $this->options['extension'] . "'...");
    	
    	// First we copy the old locale to a new directory named as 'targetLanguage'.
    	$sourceLocaleDir = $this->options['output'] . DIRECTORY_SEPARATOR . $this->options['language'];
    	$destLocaleDir = $this->options['output'] . DIRECTORY_SEPARATOR . $this->options['targetLanguage'];
    	
    	if (!tao_helpers_File::copy($sourceLocaleDir, $destLocaleDir, true, true)){
    		$this->err("Locale '" . $this->options['language'] . "' could not be copied to locale '" . $this->options['targetLanguage'] . "'.");
    	}
    	
    	// We now apply transformations to the new locale.
    	foreach (scandir($destLocaleDir) as $f){
    		$sourceLang = $this->options['language'];
    		$destLang = $this->options['targetLanguage'];
    		$qSourceLang = preg_quote($sourceLang);
    		$qDestLang = preg_quote($destLang);
    		
    		if (!is_dir($f) && $f[0] != '.'){
    			if ($f == 'messages.po'){
    				// Change the language tag in the PO file.
    				
    				$pattern = "/Language: ${qSourceLang}/u";
    				$count = 0;
    				$content = file_get_contents($destLocaleDir . DIRECTORY_SEPARATOR . 'messages.po');
    				$newFileContent = preg_replace($pattern, "Language: ${destLang}", $content, -1, $count);
    				
    				if ($count == 1){
    					$this->outVerbose("Language tag '${destLang}' applied to messages.po.");
    					file_put_contents($destLocaleDir . DIRECTORY_SEPARATOR . 'messages.po', $newFileContent);
    				}
    				else{
    					$this->err("Could not change language tag in messages.po.");
    				}
    			}
    			else if ($f == 'messages_po.js'){
    				// Change the language tag in comments.
    				// Change the langCode JS variable.
    				$pattern = "/var langCode = '${qSourceLang}';/u";
    				$count1 = 0;
    				$content = file_get_contents($destLocaleDir . DIRECTORY_SEPARATOR . 'messages_po.js');
    				$newFileContent = preg_replace($pattern, "var langCode = '${destLang}';", $content, -1, $count1);
    				
    				$pattern = "|/\\* lang: ${qSourceLang} \\*/|u";
    				$count2 = 0;
    				$newFileContent = preg_replace($pattern, "/* lang: ${destLang} */", $newFileContent, -1, $count2);
    				
    				if ($count1 + $count2 == 2){
    					$this->outVerbose("Language tag '${destLang}' applied to messages_po.js");
    					file_put_contents($destLocaleDir . DIRECTORY_SEPARATOR . 'messages_po.js', $newFileContent);
    				}
    				else{
    					$this->err("Could not change language tag in messages_po.js");
    				}
    			}
    			else if ($f == 'lang.rdf'){
    				// Change <![CDATA[XX]]>
    				// Change http://www.tao.lu/Ontologies/TAO.rdf#LangXX
    				$pattern = "/<!\\[CDATA\\[${qSourceLang}\\]\\]>/u";
    				$count1 = 0;
    				$content = file_get_contents($destLocaleDir . DIRECTORY_SEPARATOR . 'lang.rdf');
    				$newFileContent = preg_replace($pattern, "<![CDATA[${destLang}]]>", $content, -1, $count1);
    				
    				$pattern = "|http://www.tao.lu/Ontologies/TAO.rdf#Lang${qSourceLang}|u";
    				$count2 = 0;
    				$newFileContent = preg_replace($pattern, "http://www.tao.lu/Ontologies/TAO.rdf#Lang${destLang}", $newFileContent, -1, $count2);

    				$pattern = '/xml:lang="EN"/u';
    				$count3 = 0;
    				$newFileContent = preg_replace($pattern, 'xml:lang="en-US"', $newFileContent, -1, $count3);
    				
    				if ($count1 + $count2 + $count3 == 3){
    					$this->outVerbose("Language tag '${destLang}' applied to lang.rdf");
    					file_put_contents($destLocaleDir . DIRECTORY_SEPARATOR . 'lang.rdf', $newFileContent);
    				}
    				else{
    					$this->err("Could not change language tag in lang.rdf");
    				}
    			}
    			else{
    				// Check for a .rdf extension.
    				$infos = pathinfo($destLocaleDir . DIRECTORY_SEPARATOR . $f);
    				if (isset($infos['extension']) && $infos['extension'] == 'rdf'){
    					// Change annotations @sourceLanguage and @targetLanguage
    					// Change xml:lang
    					$pattern = "/@sourceLanguage EN/u";
    					$content = file_get_contents($destLocaleDir . DIRECTORY_SEPARATOR . $f);
    					$newFileContent = preg_replace($pattern, "@sourceLanguage en-US", $content);
    					
    					$pattern = "/@targetLanguage ${qSourceLang}/u";
    					$newFileContent = preg_replace($pattern, "@targetLanguage ${destLang}", $newFileContent);
    					
    					$pattern = '/xml:lang="' . $qSourceLang . '"/u';
    					$newFileContent = preg_replace($pattern, 'xml:lang="' . $destLang . '"', $newFileContent);
    					
    					$this->outVerbose("Language tag '${destLang}' applied to ${f}");
    					file_put_contents($destLocaleDir . DIRECTORY_SEPARATOR . $f, $newFileContent);
    				}
    			}
    		}
    	}
    }

    /**
     * Builds the path to files dedicated to a given language (locale) for a
     * extension ID.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string extension
     * @param  string language
     * @return string
     */
    private function buildLanguagePath($extension, $language)
    {
        $returnValue = (string) '';

        
        $returnValue = dirname(__FILE__) . '/../../' . $extension . '/' . self::DEF_OUTPUT_DIR . '/' . $language;
        

        return (string) $returnValue;
    }

    /**
     * Find a structure.xml manifest in a given directory.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string directory
     * @return mixed
     */
    public function findStructureManifest($directory = null)
    {
        $returnValue = null;

        
        if ($directory == null) {
        	$actionsDir = $this->options['input'] . '/actions';	
        } else {
        	$actionsDir = $directory . '/actions';
        }
        
        $dirEntries = scandir($actionsDir);
        
        if ($dirEntries === false) {
        	$returnValue = false;	
        } else {
        	$structureFile = null;
        	foreach ($dirEntries as $f) {
				if (preg_match("/(.*)structure\.xml$/", $f)) {
					$structureFile = $f;
					break;
				}
        	}
        	
        	if ($structureFile === null) {
        		$returnValue = false;
        	} else {
        		$returnValue = $structureFile;
        	}
        }
        

        return $returnValue;
    }

	/**
	 * Prepare a PO file before output by adding headers to it.
	 *
	 * @access public
	 * @author Joel Bout, <joel.bout@tudor.lu>
	 * @param tao_helpers_translation_POFile $poFile
	 * @param bool $poEditorReady
	 * @return void
	 */
    public function preparePOFile( tao_helpers_translation_POFile $poFile, $poEditorReady = false)
    {
        
        $poFile->addHeader('Project-Id-Version', PRODUCT_NAME . ' ' . TAO_VERSION_NAME);
        $poFile->addHeader('PO-Revision-Date', date('Y-m-d') . 'T' . date('H:i:s'));
        $poFile->addHeader('Last-Translator', 'TAO Translation Team <translation@tao.lu>');
        $poFile->addHeader('MIME-Version', '1.0');
        $poFile->addHeader('Language', $poFile->getTargetLanguage());
        $poFile->addHeader('sourceLanguage', $poFile->getSourceLanguage());
        $poFile->addHeader('targetLanguage', $poFile->getTargetLanguage());
        $poFile->addHeader('Content-Type', 'text/plain; charset=utf-8');
        $poFile->addHeader('Content-Transfer-Encoding', '8bit');

		if ($poEditorReady) {
			$poFile->addHeader('X-Poedit-Basepath', '../../');
			$poFile->addHeader('X-Poedit-KeywordsList', '__');
			$poFile->addHeader('X-Poedit-SearchPath-0', '.');
		}
    }

    /**
     * Determines if an given directory actually contains a TAO extension.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string directory
     * @return boolean
     */
    public function isExtension($directory)
    {
        $returnValue = (bool) false;

        
        $hasStructure = $this->findStructureManifest($directory) !== false;
        $hasPHPManifest = false;
        
        $files = scandir($this->options['input']);
        if ($files !== false) {
        	foreach ($files as $f) {
				if (is_file($this->options['input'] . '/' .$f) && is_readable($this->options['input'] . '/' . $f)) {
					if ($f == 'manifest.php') {
						$hasPHPManifest = true;
					}
				}
        	}
        }
        
        $returnValue = $hasStructure || $hasPHPManifest;
        

        return (bool) $returnValue;
    }

    /**
     * Add translations as translation units found in a structure.xml manifest
     * a given PO file.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param tao_helpers_translation_POFile $poFile
     * @throws Exception
     * @return void
     */
    public function addManifestsTranslations( tao_helpers_translation_POFile $poFile)
    {
        
        $this->outVerbose("Adding all manifests messages to extension '" . $this->options['extension'] . "'");
    	
        $rootDir = dirname(__FILE__) . '/../../';
        $directories = scandir($rootDir);
        $exceptions = array('generis', 'tao', '.*');
        
        if (false === $directories) {
        	$this->err("The TAO root directory is not readable. Please check permissions on this directory.", true);	
        } else {
        	foreach ($directories as $dir) {
				if (is_dir($rootDir . $dir) && !in_array($dir, $exceptions)) {
					// Maybe it should be read.
					if (in_array('.*', $exceptions) && $dir[0] == '.') {
						continue;	
					} else {
						// Is this a TAO extension ?
					    $file = MenuService::getStructuresFilePath($this->options['extension']);
					    if (!is_null($file)) {
					        $structureExtractor = new tao_helpers_translation_StructureExtractor(array($file));
						    $structureExtractor->extract();
					    	$poFile->addTranslationUnits($structureExtractor->getTranslationUnits());
							$this->outVerbose("Manifest of extension '" . $dir . "' added to extension '" . $this->options['extension'] . "'");
						}
					}
				}
        	}
        }
        
    }

    /**
     * Add the requested language in the ontology. It will used the parameters
     * the command line for logic.
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    protected function addLanguageToOntology()
    {
        
        $this->outVerbose("Importing RDF language description '" . $this->options['language'] . "' to ontology...");
        
        // RDF Language Descriptions are stored in the tao meta-extension locales.
        $expectedDescriptionPath = $this->buildLanguagePath('tao', $this->options['language']) . '/lang.rdf';
        
        if (file_exists($expectedDescriptionPath)){
            if (is_readable($expectedDescriptionPath)){
                
                // Let's remove any instance of the language description before inserting the new one.
                $taoNS = 'http://www.tao.lu/Ontologies/TAO.rdf#';
                $expectedLangUri = $taoNS . 'Lang' . $this->options['language'];
                $lgDescription = new core_kernel_classes_Resource($expectedLangUri);
                
                if ($lgDescription->exists()){
                    $lgDescription->delete();
                    $this->outVerbose("Existing RDF Description language '" . $this->options['language'] . "' deleted.");
                }
                
                $generisAdapterRdf = new tao_helpers_data_GenerisAdapterRdf();
                if (true === $generisAdapterRdf->import($expectedDescriptionPath, null, LOCAL_NAMESPACE)){
                    $this->outVerbose("RDF language description '" . $this->options['language'] . "' successfully imported.");
                }else{
                    $this->err("An error occured while importing the RDF language description '" . $this->options['language'] . "'.", true);
                }
                
            }else{
                $this->err("RDF language description (lang.rdf) cannot be read in meta-extension 'tao' for language '" . $this->options['language'] . "'.", true);
            }
        }else{
            $this->err("RDF language description (lang.rdf) not found in meta-extension 'tao' for language '" . $this->options['language'] . "'.", true);
        }
        
        
        $this->outVerbose("RDF language description '" . $this->options['language'] . "' added to ontology.");
        
    }

    /**
     * Removes a language from the Ontology and all triples with the related
     * tag. Will use command line parameters for logic.
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    protected function removeLanguageFromOntology()
    {
        
        $this->outVerbose("Removing RDF language description '" . $this->options['language'] . "' from ontology...");
        $taoNS = 'http://www.tao.lu/Ontologies/TAO.rdf#';
        $expectedDescriptionUri = $taoNS . 'Lang' . $this->options['language'];
        $lgResource = new core_kernel_classes_Resource($expectedDescriptionUri);
        
        if (true === $lgResource->exists()) {
            $lgResource->delete();
            $this->outVerbose("RDF language description '" . $this->options['language'] . "' successfully removed.");
        }else{
            $this->outVerbose("RDF language description '" . $this->options['language'] . "' not found but considered removed.");
        }
        
    }

    /**
     * Checks authentication parameters for the TAO API.
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    protected function checkAuthInput()
    {
        
        $defaults = array('user' => null,
						  'password' => null);
						  
		$this->options = array_merge($defaults, $this->options);
		
		if ($this->options['user'] == null) {
			$this->err("Please provide a value for the 'user' parameter.", true);
		}
		else if ($this->options['password'] == null) {
			$this->err("Please provide a value for the 'password' parameter.", true);
		}
        
    }

    /**
     * Get the ontology file paths for a given extension, sorted by target name
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    private function getOntologyFiles()
    {
        $returnValue = array();

        
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById($this->options['extension']);
        $returnValue = $ext->getManifest()->getInstallModelFiles();
        

        return (array) $returnValue;
    }

    /**
     * Check inputs for the 'enable' action.
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkEnableInput()
    {
        
        $this->checkAuthInput();
        $defaults = array('language' => null,
                          'input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
                          'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR);
                          
        $this->options = array_merge($defaults, $this->options);
        
        if ($this->options['language'] == null){
            $this->err("Please provide the 'language' parameter.", true);
        }
        
    }

    /**
     * Short description of method checkDisableInput
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkDisableInput()
    {
        
        $this->checkAuthInput();
        $defaults = array('language' => null);
        $this->options = array_merge($defaults, $this->options);
        if ($this->options['language'] == null){
            $this->err("Please provide the 'language' parameter.", true);
        }
        
    }

    /**
     * Short description of method actionEnable
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionEnable()
    {
        
        $userService = tao_models_classes_UserService::singleton();
        $this->outVerbose("Connecting to TAO as '" . $this->options['user'] . "' ...");
        if ($userService->loginUser($this->options['user'], $this->options['password'])){
            $this->outVerbose("Connected to TAO as '" . $this->options['user'] . "'.");
            $this->addLanguageToOntology();
            $userService->logout();
            $this->outVerbose("Disconnected from TAO.");
        }else{
            $this->err("Unable to connect to TAO as '" . $this->options['user'] . "'. Please check user name and password.", true);
        }
        
    }

    /**
     * Implementation of the 'disable' action. When this action is called, a
     * Language Description ('language' param) is removed from the Knowledge
     * The language is at this time not available anymore to end-users. However,
     * Triples that had a corresponding language tag are not remove from the
     * If the language is enabled again via the 'enable' action of this script,
     * having corresponding languages will be reachable again.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionDisable()
    {
        
        $userService = tao_models_classes_UserService::singleton();
        $this->outVerbose("Connecting to TAO as '" . $this->options['user'] . "' ...");
        if ($userService->loginUser($this->options['user'], $this->options['password'])){
            $this->outVerbose("Connected to TAO as '" . $this->options['user'] . "'.");
            $this->removeLanguageFromOntology();
            $userService->logout();
            $this->outVerbose("Disconnected from TAO.");
        }else{
            $this->err("Unable to connect to TAO as '" . $this->options['user'] . "'. Please check user name and password.", true);
        }
        
    }

    /**
     * Implementation of the 'compile' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionCompile()
    {
        
        $extensionsToCreate = explode(',', $this->options['extension']);
        $extensionsToCreate = array_unique($extensionsToCreate);
        
		foreach ($extensionsToCreate as $extension){
			$language = $this->options['language'];
			$compiledTranslationFile = new tao_helpers_translation_TranslationFile();
			$compiledTranslationFile->setTargetLanguage($this->options['language']);
			
			$this->outVerbose("Compiling language '${language}' for extension '${extension}'...");
			
			// Get the dependencies of the target extension.
			// @todo Deal with dependencies at compilation time.
			$dependencies = array();
			if ($extension !== 'tao'){
				$dependencies[] = 'tao';
			}
			
			$this->outVerbose("Resolving Dependencies...");
			foreach ($dependencies as $depExtId){
				$this->outVerbose("Adding messages from extension '${depExtId}' in '${language}'...");
				// Does the locale exist for $depExtId?
				$depPath = $this->buildLanguagePath($depExtId, $language) . '/' . self::DEF_PO_FILENAME;
				if (!file_exists($depPath) || !is_readable($depPath)){
					$this->outVerbose("Dependency on extension '${depExtId}' in '${language}' does not exist. Trying to resolve default language...");
					$depPath = $this->buildLanguagePath($depExtId, tao_helpers_translation_Utils::getDefaultLanguage() . '/' . self::DEF_PO_FILENAME);

					if (!file_exists($depPath) || !is_readable($depPath)){
						$this->outVerbose("Dependency on extension '${depExtId}' in '${language}' does not exist.");
						continue;
					}
				}
				
				// Recompile the dependent extension (for the moment 'tao' meta-extension only).
				$oldVerbose = $this->options['verbose'];
				$this->parameters['verbose'] = false;
				$this->options['extension'] = $depExtId;
				$this->actionCompile();
				$this->options['extension'] = $extension;
				$this->parameters['verbose'] = $oldVerbose;
				
				$poFileReader = new tao_helpers_translation_POFileReader($depPath);
				$poFileReader->read();
				$poFile = $poFileReader->getTranslationFile();
				$poCount = $poFile->count();
				$compiledTranslationFile->addTranslationUnits($poFile->getTranslationUnits());
				
				$this->outVerbose("${poCount} messages added.");
			}

			if (($extDirectories = scandir(ROOT_PATH)) !== false){
						
				// Get all public messages accross extensions.
				foreach ($extDirectories as $extDir){
					$extPath = ROOT_PATH . '/' . $extDir;
					
					if (is_dir($extPath) && is_readable($extPath) && $extDir[0] != '.' && !in_array($extDir, $dependencies) && $extDir != $extension && $extDir != 'generis'){
						$this->outVerbose("Adding public messages from extension '${extDir}' in '${language}'...");
						
						$poPath = $this->buildLanguagePath($extDir, $language) . '/' . self::DEF_PO_FILENAME;
						if (!file_exists($poPath) || !is_readable($poPath)){
							$this->outVerbose("Extension '${extDir}' is not translated in language '${language}'. Trying to retrieve default language...");
							$poPath = $this->buildLanguagePath($extDir, tao_helpers_translation_Utils::getDefaultLanguage()) . '/' . self::DEF_PO_FILENAME;
							
							if (!file_exists($poPath) || !is_readable($poPath)){
								$this->outVerbose("Extension '${extDir}' in '${language}' does not exist.");
								continue;
							}
						}

						$poFileReader = new tao_helpers_translation_POFileReader($poPath);
						$poFileReader->read();
						$poFile = $poFileReader->getTranslationFile();
						$poUnits = $poFile->getByFlag('tao-public');
						$poCount = count($poUnits);
						$compiledTranslationFile->addTranslationUnits($poUnits);
						$this->outVerbose("${poCount} public messages added.");
					}
				}                
				
				// Finally, add the translation units related to the target extension.
				$path = $this->buildLanguagePath($extension, $language) . '/' . self::DEF_PO_FILENAME;
				if (file_exists($path) && is_readable($path)){
					$poFileReader = new tao_helpers_translation_POFileReader($path);
					$poFileReader->read();
					$poFile = $poFileReader->getTranslationFile();
					$compiledTranslationFile->addTranslationUnits($poFile->getTranslationUnits());
					
					// Sort the TranslationUnits.
					$sortingMethod = tao_helpers_translation_TranslationFile::SORT_ASC_I;
					$compiledTranslationFile->setTranslationUnits($compiledTranslationFile->sortBySource($sortingMethod));
					
					$phpPath = $this->buildLanguagePath($extension, $language) . '/' . self::DEF_PHP_FILENAME;
					$phpFileWriter = new tao_helpers_translation_PHPFileWriter($phpPath, $compiledTranslationFile);
					$phpFileWriter->write();
					$this->outVerbose("PHP compiled translations for extension '${extension}' with '${language}' written.");
					
					$jsPath = $this->buildLanguagePath($extension, $language) . '/' . self::DEF_JS_FILENAME;
					$jsFileWriter = new tao_helpers_translation_JSFileWriter($jsPath, $compiledTranslationFile);
					$jsFileWriter->write();
					$this->outVerbose("JavaScript compiled translations for extension '${extension}' with '${language}' written.");
				}
				else{
					$this->err("PO file '${path}' for extension '${extension}' with language '${language}' cannot be read.", true);
				}
			}
			else{
				$this->err("Cannot list TAO Extensions from root path. Check your system rights.", true);    
			}

			$this->outVerbose("Translations for '${extension}' with language '${language}' gracefully compiled.");

		}
        
    }

    /**
     * Checks the input for the 'compile' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkCompileInput()
    {
        
        $defaults = array('extension' => null,
                          'language' => null,
                          'input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
                          'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR);
                          
        $this->options = array_merge($defaults, $this->options);
        
        if ($this->options['extension'] == null){
            $this->err("Please provide the 'extension' parameter.", true);
        }
        else if ($this->options['language'] == null){
            $this->err("Please provide the 'language' parameter.", true);
        }
        
    }

    /**
     * Implementation of the 'compileAll' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function actionCompileAll()
    {
        
        // Get the list of languages that will be compiled.
        $this->outVerbose("Compiling all languages for extension '" . $this->options['extension'] . "'...");
        
        $rootDir = ROOT_PATH;
        $extensionDir = $rootDir . '/' . $this->options['extension'];
        $localesDir = $extensionDir . '/locales';
        $locales = array();
        
        $directories = scandir($localesDir);
        if ($directories === false) {
            $this->err("The locales directory of extension '" . $this->options['extension'] . "' cannot be read.", true);    
        } else {
            foreach ($directories as $dir) {
                if ($dir[0] !== '.') {
                    // It is a language directory.
                    $locales[] = $dir;
                }
            }
        }
        
        foreach ($locales as $l) {
            $this->options['language'] = $l;
            $this->checkCompileInput();
            $this->actionCompile();
            
            $this->outVerbose("");
        }
        
    }

    /**
     * Checks the input of the 'compileAll' action.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function checkCompileAllInput()
    {
        
        $defaults = array('extension' => null,
                          'input' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_INPUT_DIR,
                          'output' => dirname(__FILE__) . '/../../' . $this->options['extension'] . '/' . self::DEF_OUTPUT_DIR);
                          
        $this->options = array_merge($defaults, $this->options);
        
        if ($this->options['extension'] == null){
            $this->err("Please provide the 'extension' parameter.", true);
        }
        
    }
    
    private function actionGetExt()
    {
		$rootDir = ROOT_PATH;
		$extensions = array();
		$extensionsList = '';
		$language = $this->options['language'];

		// create initial folders array
		if ($dir = opendir($rootDir)) {
			$j = 0;
			while (($file = readdir($dir)) !== false) {
				if ($file[0] !== '.' && $file != '.' && $file != '..' && is_dir($rootDir.$file)) {
					$j++;
					$directories[$j] = $file;
				}
			}
		}

		if ($directories === false) {
			
			$this->err("The locales directory of extension '" . $this->options['extension'] . "' cannot be read.", true);    
		} else {
			
			foreach ($directories as $dir) {

				// folder where these .po files should be
				$extensionLocalesDir = $dir . '/' . self::DEF_OUTPUT_DIR . '/' . $language;
				$poFile = $rootDir . $extensionLocalesDir . '/' . self::DEF_PO_FILENAME;
				
				// check .po file existency
				if (file_exists($poFile) && is_readable($poFile)){

					$extensions[] = $dir;
					$extensionsList .= (!$extensionsList?"":",") . $dir;
				}
			}
			sort($extensions);
			print_r( $extensions );
			echo ( count($extensions) . ' extensions with translations: ' . $extensionsList . "\n");
		}
	}

	/**
	 * @param $f
	 * @param $translatableProperties
	 * @return tao_helpers_translation_POFile
	 * @throws tao_helpers_translation_TranslationException
	 */
	protected function extractPoFileFromRDF($f, $translatableProperties)
	{
		$modelExtractor = new tao_helpers_translation_POExtractor(array($f));
		$modelExtractor->setTranslatableProperties($translatableProperties);
		$modelExtractor->extract();

		$translationFile = new tao_helpers_translation_POFile();
		$translationFile->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
		$translationFile->setTargetLanguage($this->options['language']);
		$translationFile->addTranslationUnits($modelExtractor->getTranslationUnits());
		$translationFile->setExtensionId($this->options['extension']);

		$this->preparePOFile($translationFile);
		return $translationFile;
	}

    protected function checkInputOption()
	{
		if (!is_null($this->options['input'])) {
			if (!is_dir($this->options['input'])) {
				$this->err("The 'input' parameter you provided is not a directory.", true);
			} else {
				if (!is_readable($this->options['input'])) {
					$this->err("The 'input' directory is not readable.", true);
				}
			}
		}
	}

    protected function checkOutputOption()
	{
		if (!is_null($this->options['output'])) {
			if (!is_dir($this->options['output'])) {
				$this->err("The 'output' parameter you provided is not a directory.", true);
			} else {
				if (!is_writable($this->options['output'])) {
					$this->err("The 'output' directory is not writable.", true);
				}
			}
		}
	}

	/**
	 * @return array
	 * @throws Exception
	 */
    protected function getLanguageList()
	{
		$rootDir = dirname(__FILE__) . '/../..';
		$extensionDir = $rootDir . '/' . $this->options['extension'];
		$localesDir = $extensionDir . '/locales';
		$locales = array();

		$directories = scandir($localesDir);
		if ($directories === false) {
			$this->err("The locales directory of extension '" . $this->options['extension'] . "' cannot be read.", true);
			return $locales;
		} else {
			foreach ($directories as $dir) {
				if ($dir[0] !== '.') {
					// It is a language directory.
					$locales[] = $dir;
				}
			}
			return $locales;
		}
	}

    /**
     *
     * @param $f filename
     * @return string
     */
    protected  function getOntologyPOFileName($f){
        return basename($f) . '.po';
    }

}
