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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Internationalization helper.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_I18n
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute AVAILABLE_LANGS_CACHEKEY
     *
     * @access private
     * @var string
     */
    const AVAILABLE_LANGS_CACHEKEY = 'i18n_available_langs';

    /**
     * Short description of attribute availableLangs
     *
     * @access protected
     * @var array
     */
    protected static $availableLangs = array();

    // --- OPERATIONS ---

    /**
     * Load the translation strings
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  common_ext_Extension $extension
     * @param  string langCode
     * @return mixed
     */
    public static function init(common_ext_Extension $extension, $langCode)
    {
    	// if the langCode is empty do nothing
    	if (empty($langCode)){
    		throw new Exception("Language is not defined");
    	}
    	
		//init the ClearFw l10n tools
		l10n::init();
		
		$basePath = $extension->getDir();

		if (!empty($_GET['ext']) && is_string($_GET['ext'])){
			$shownExtension = common_ext_ExtensionsManager::singleton()->getExtensionById($_GET['ext']);
			if (!empty($shownExtension)){
				try{
					$basePath = $shownExtension->getDir();
					$baseUrl = $shownExtension->getConstant('BASE_URL');
				}
				catch (common_exception_Error $e){
					// let the current base path be used...
				}
			}
		}
		
		l10n::set($basePath . 'locales' . DIRECTORY_SEPARATOR . $langCode. DIRECTORY_SEPARATOR . 'messages');
    }

    /**
     * Returns the current interface language for backwards compatibility
     *
     * @access public
     * @return string
     */
    public static function getLangCode()
    {
        return common_session_SessionManager::getSession()->getInterfaceLanguage();
    }

    /**
     * Returns the code of a resource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public static function getLangResourceByCode($code)
    {
        $langs = self::getAvailableLangs();
        return isset($langs[$code]) ? new core_kernel_classes_Resource($langs[$code]['uri']) : null; 
    }
    
    /**
     * @param unknown $code
     * @return boolean
     */
    public static function isLanguageRightToLeft($code)
    {
        $orientation = null;
        $langs = self::getAvailableLangs();
        $orientation = isset($langs[$code]) ? $langs[$code][PROPERTY_LANGUAGE_ORIENTATION] : null; 
        return $orientation == INSTANCE_ORIENTATION_RTL;
    }

    /**
     * This method returns the languages available in TAO.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  boolean langName If set to true, an associative array where keys are language codes and values are language labels. If set to false (default), a simple array of language codes is returned.
     * @return array
     * @throws common_exception_InconsistentData
     */
    private static function getAvailableLangs()
    {
        //get it into the api only once 
        if(count(self::$availableLangs) == 0){
        	try {
        		self::$availableLangs = common_cache_FileCache::singleton()->get(self::AVAILABLE_LANGS_CACHEKEY);
        	} catch (common_cache_NotFoundException $e) {
	        	$langClass = new core_kernel_classes_Class(CLASS_LANGUAGES);
	        	$valueProperty = new core_kernel_classes_Property(RDF_VALUE);
	        	foreach($langClass->getInstances() as $lang){
	        	    $values = $lang->getPropertiesValues(array(
	        	    	RDF_VALUE,
	        	        PROPERTY_LANGUAGE_USAGES,
	        	        PROPERTY_LANGUAGE_ORIENTATION
	        	    ));
	        	    if (count($values[RDF_VALUE]) != 1) {
	        	        throw new common_exception_InconsistentData('Error with value of language '.$lang->getUri());
	        	    }
	        	    $value = current($values[RDF_VALUE])->__toString();
	        	    $usages = array();
	        	    foreach ($values[PROPERTY_LANGUAGE_USAGES] as $usage) {
	        	        $usages[] = $usage->getUri();
	        	    }
	        	    if (count($values[PROPERTY_LANGUAGE_ORIENTATION]) != 1) {
	        	        common_Logger::w('Error with orientation of language '.$lang->getUri());
	        	        $orientation = INSTANCE_ORIENTATION_LTR;
	        	    } else {
                        $orientation = current($values[PROPERTY_LANGUAGE_ORIENTATION])->getUri();
	        	    }
	        	    self::$availableLangs[$value] = array(
	        	        'uri'                         => $lang->getUri(), 
	        	        PROPERTY_LANGUAGE_USAGES      => $usages,
	        	        PROPERTY_LANGUAGE_ORIENTATION => $orientation
	        	    );
	        	}
	        	common_cache_FileCache::singleton()->put(self::$availableLangs, self::AVAILABLE_LANGS_CACHEKEY);
        	}
        }
        
        return self::$availableLangs;
    }

    /**
     * Get available languages from the knownledge base depending on a specific usage.
     * 
     * By default, TAO considers two built-in usages:
     * 
     * * GUI Language ('http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsageGUI')
     * * Data Language ('http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsageData')
     *
     * @author Jérôme Bogaerts <jerome@taotesting.com>
     * @param core_kernel_classes_Resource $usage Resource usage An instance of tao:LanguagesUsages from the knowledge base.
     * @return array An associative array of core_kernel_classes_Resource objects index by language code.
     */
    public static function getAvailableLangsByUsage( core_kernel_classes_Resource $usage)
    {
        $returnValue = array();
        
        foreach (self::getAvailableLangs() as $code => $langData) {
            if (in_array($usage->getUri(), $langData[PROPERTY_LANGUAGE_USAGES])) {
                $lang = new core_kernel_classes_Resource($langData['uri']);
                $returnValue[$code] = $lang->getLabel();
            }
        }
        return $returnValue;
    }

}
