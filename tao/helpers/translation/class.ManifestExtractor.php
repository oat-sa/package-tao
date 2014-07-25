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

require_once('tao/helpers/translation/class.TranslationExtractor.php');

/**
 * The ManifestExtractor extracts translation units from structures.xml files.
 * It will extract the following nodes/attributes.
 * 
 * - //structure[@name] ('tao-public' flag)
 * - //section[@name] ('tao-public' flag)
 * - //action[@name]
 * - //tree[@name]
 * - //description ('tao-public' flag)
 * 
 * Some extracted translation units will be given a 'tao-public' flag. This flag indicates
 * that the translation unit has to be included in every compiled messages.po file.
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_ManifestExtractor
    extends tao_helpers_translation_TranslationExtractor
{

    /**
     * Extracts the translation units from a structures.xml file.
     * Translation Units can be retrieved after extraction by calling the getTranslationUnits
     * method.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @throws tao_helpers_translation_TranslationException If an error occurs.
     */
    public function extract()
    {
        $paths = $this->getPaths();
        $translationUnits = array();
        
        foreach ($paths as $path) {
        	// Search for a filename containing 'structure.xml'.
        	if (is_dir($path)) {
        		$files = scandir($path);
        		
        		foreach ($files as $file) {
        			$fullPath = $path . '/' . $file;
        			if (is_file($fullPath) && mb_strpos($file, 'structures.xml') !== false) {
						// Translations must be extracted from this tao manifest file.
						try{
							$xml = new SimpleXMLElement(trim(file_get_contents($fullPath)));
							if ($xml instanceof SimpleXMLElement){
								// look up for "name" attributes of structure elements.
								$nodes = $xml->xpath("//structure[@name]|//section[@name]");
								foreach ($nodes as $node) {
									if (isset($node['name'])) {
										$nodeName = (string)$node['name'];
                                        $newTranslationUnit = new tao_helpers_translation_POTranslationUnit();
                                        $newTranslationUnit->setSource($nodeName);
                                        $newTranslationUnit->addFlag('tao-public');
										$translationUnits[$nodeName] = $newTranslationUnit;
									}
								}
                                
                                // look up for "name" attributes of action elements.
                                $nodes = $xml->xpath("//action[@name]|//tree[@name]");
                                foreach ($nodes as $node) {
                                    if (isset($node['name'])) {
                                        $nodeName = (string)$node['name'];
                                        $newTranslationUnit = new tao_helpers_translation_POTranslationUnit();
                                        $newTranslationUnit->setSource($nodeName);
                                        $translationUnits[$nodeName] = $newTranslationUnit;
                                    }
                                }
								
								// look up for "description" elements.
								$nodes = $xml->xpath("//description");
								foreach ($nodes as $node) {
									if ((string)$node != '') {
									    $newTranslationUnit = new tao_helpers_translation_POTranslationUnit();
                                        $newTranslationUnit->setSource((string)$node);
                                        $newTranslationUnit->addFlag('tao-public');
										$translationUnits[(string)$node] = $newTranslationUnit;
									}
								}
							}
						}
						catch(Exception $e){}
        			}
        		}
        	} else {
        		throw new tao_helpers_translation_TranslationException("'${path}' is not a directory.");
        	}
        }
        
        $this->setTranslationUnits(array_values($translationUnits));
    }

}

?>