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
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * 
 * @author Jerome Bogaerts, <taosupport@tudor.lu>
 * @package tao
 
 */
class InstallTest extends TaoPhpUnitTestRunner {
    
    const SAMPLE_LOCALES = '/samples/locales';
	
	/**
	 * Test the Installation Model Creator.
	 */
	public function testModelCreator() {
	    // - Test the existence of translations for models in tao meta extension.
		$extensionManager = common_ext_ExtensionsManager::singleton();
		$extensions = $extensionManager->getInstalledExtensions();
		$taoNs = LOCAL_NAMESPACE;
		
		// - Test the existence of language descriptions.
		$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
		$langs = $modelCreator->getLanguageModels();
        $this->assertTrue(isset($langs[$taoNs . '#']), "No language descriptions available for model '${taoNs}'.");
        // We should have at least english described.
        $enFound = false;
        $languageDescriptionFiles = $langs[$taoNs . '#'];
        foreach ($languageDescriptionFiles as $f){
            if(preg_match('/locales\/'.DEFAULT_LANG.'\/lang.rdf/i', $f)){
                $enFound = true;
                break;
            }
        }
        
        $this->assertTrue($enFound, "English language description not found for model '${taoNs}'.");
	}

    /**
     * This test aims at testing the tao_install_utils_System class methods.
     */
    public function testSystemUtils(){
        // - Check if tao platform locales can be correctly retrieved.
        $locales = tao_install_utils_System::getAvailableLocales(dirname(__FILE__) . self::SAMPLE_LOCALES);
        $this->assertTrue(is_array($locales), 'Locales should be returned as an array of strings.');
        $this->assertTrue(array_key_exists('EN', $locales), "Locale 'EN' should be found.");
        $this->assertTrue($locales['EN'] == 'English', "Wrong label for locale 'EN'.");
        $this->assertTrue(array_key_exists('DE', $locales), "Locale 'DE' should be found.");
        $this->assertTrue($locales['DE'] == 'German', "Wrong label for locale 'DE'.");
        $this->assertTrue(array_key_exists('FR', $locales), "Locale 'FR' should be found.");
        $this->assertTrue($locales['FR'] == 'French', "Wrong label for locale 'FR'.");
        $this->assertTrue(array_key_exists('LU', $locales), "Locale 'LU' should be found.");
        $this->assertTrue($locales['LU'] == 'Luxembourgish', "Wrong label for locale 'LU'.");
        $this->assertTrue(array_key_exists('SE', $locales), "Locale 'SE' should be found.");
        $this->assertTrue($locales['SE'] == 'Swedish', "Wrong label for locale 'SE'.");
        $this->assertTrue(array_key_exists('en-YO', $locales), "Locale 'en-YO' should be found.");
        $this->assertTrue($locales['en-YO'] == 'Yoda English', "Wrong label for locale 'en-YO'.");
    }
}