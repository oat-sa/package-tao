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
?>
<?php
/**
 * This class aims at providing utilities about the current installation from
 * the host system and its filesystem, including the tao platform directory.
 * 
 * @author Somsack Sipasseuth <somsack.sipasseuth@tudor.lu>
 * @author Jerome Bogaerts <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_install_utils_System{
	
	/**
	 * Get informations on the host system.
     * 
	 * @return array where key/values are 'folder' as string, 'host' as string, 'https' as boolean.
	 */
	public static function getInfos(){
                
                
		//subfolder shall be detected as /SUBFLODERS/tao/install/index.php so we remove the "/extension/module/action" part:
        $subfolder = $_SERVER['REQUEST_URI'];
        $subfolder = preg_replace('/\/(([^\/]*)\/){2}([^\/]*)$/', '', $subfolder);
        $subfolder = preg_replace('/^\//', '', $subfolder);
        
        return array(
			'folder'	=> $subfolder,
			'host'		=> $_SERVER['HTTP_HOST'],
			'https'		=> ($_SERVER['SERVER_PORT'] == 443) 
		);
	}
	
	/**
	 * Check if TAO is already installed.
     * 
	 * @return boolean
	 */
	public static function isTAOInstalled(){
		$config = dirname(__FILE__).'/../../../config/generis.conf.php';
		return file_exists($config);
	}
    
    /**
     * Returns the availables locales (languages or cultures) of the tao platform
     * on the basis of a particular locale folder e.g.  the /locales folder of the tao
     * meta-extension.
     * 
     * A locale will be included in the resulting array only if a valid 'lang.rdf'
     * file is found.
     * 
     * @param string $path The location of the /locales folder to inspect.
     * @return array An array of strings where keys are the language code and values the language label.
     * @throws UnexpectedValueException
     */
    public static function getAvailableLocales($path){
        $locales = @scandir($path);
        $returnValue = array();
        
        if ($locales !== false){
            foreach ($locales as $l){
                if ($l[0] !== '.'){
                    // We found a locale folder. Does it contain a valid lang.rdf file?
                    $langFilePath = $path . '/' . $l . '/lang.rdf';
                    if (is_file($langFilePath) && is_readable($langFilePath)){
                        try{
                            $doc = new DOMDocument('1.0', 'UTF-8');
                            $doc->load($langFilePath);
                            $xpath = new DOMXPath($doc);
                            $xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
                            $xpath->registerNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
                            $expectedUri = 'http://www.tao.lu/Ontologies/TAO.rdf#Lang' . $l;
                            
                            // Look for an rdf:value equals to the folder name.
                            $rdfValues = $xpath->query("//rdf:Description[@rdf:about='${expectedUri}']/rdf:value");
                            if ($rdfValues->length == 1 && $rdfValues->item(0)->nodeValue == $l){
                                $key = $l;
                                
                                $rdfsLabels = $xpath->query("//rdf:Description[@rdf:about='${expectedUri}']/rdfs:label[@xml:lang='en-US']");
                                if ($rdfsLabels->length == 1){
                                    $value = $rdfsLabels->item(0)->nodeValue;
                                    $returnValue[$l] = $value;
                                }
                            }
                        }
                        catch (DOMException $e){
                            // Invalid lang.rdf file, we continue to look for other ones.
                            continue;
                        }    
                    }
                }
            }

            return $returnValue;
        }else{
            throw new UnexpectedValueException("Unable to list locales in '${path}'.");
        }
    }
}
?>