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
 * 
 */

/**
 * Short description of class tao_helpers_translation_RDFTranslationFile
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_translation_RDFTranslationFile
    extends tao_helpers_translation_TaoTranslationFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute namespaces
     *
     * @access private
     * @var array
     */
    private $namespaces = array(array('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#'));

    /**
     * The namespace to which the translations belongs to.
     *
     * @access private
     * @var string
     */
    private $base = '';

    // --- OPERATIONS ---

    /**
     * Short description of method addTranslationUnit
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return mixed
     */
    public function addTranslationUnit( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        
        // We override the default behaviour because for RDFTranslationFiles, TranslationUnits are
        // unique by concatening the following attributes:
        // - RDFTranslationUnit::subject
        // - RDFTranslationUnit::predicate
        // - RDFTranslationUnit::targetLanguage
        foreach ($this->getTranslationUnits() as $tu) {
        	if ($tu->hasSameTranslationUnitSubject($translationUnit) &&
        		$tu->hasSameTranslationUnitPredicate($translationUnit) &&
        		$tu->hasSameTranslationUnitTargetLanguage($translationUnit)) {
					// This TU already exists. We change its target if the new one
					// has one.
					if ($translationUnit->getTarget() != $translationUnit->getSource()){
					    $tu->setTarget($translationUnit->getTarget());
					}
					return;
				}
        }
		
		// If we are executing this, we can add the TranslationUnit to this TranslationFile.
		$translationUnit->setSourceLanguage($this->getSourceLanguage());
        $translationUnit->setTargetLanguage($this->getTargetLanguage());
        $tus = $this->getTranslationUnits();
		array_push($tus, $translationUnit);
        $this->setTranslationUnits($tus);
        
    }

    /**
     * Sets the namespaces list of the TranslationFile. The namespace array must
     * an array of array formated like this: array(array('nsprefix' => 'URI'),
     * Former namespaces will be removed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array namespaces
     * @return void
     */
    public function setNamespaces($namespaces)
    {
        
        $this->namespaces = $namespaces;
        
    }

    /**
     * Adds a namespace to the namespaces list. The $namespace array must be
     * like array('nsprefix' => 'uri'). If the 'nsprefix' value matches an
     * set namespace, it will be updated with the new 'uri'.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array namespace
     * @return void
     */
    public function addNamespace($namespace)
    {
        
        foreach ($this->getNamespaces() as $ns) {
        	if ($ns['prefix'] == $namespace['prefix']) {
        		// This namespace is already registered.
        		return;
        	}
        }
		
		array_push($this->namespaces, $namespace);
        
    }

    /**
     * Removes a namespace in the list of namespaces. The $namespace array must
     * formatted like array('nsprefix' => 'uri'). If the 'nsprefix' cannot be
     * nothing happens. If found, the related namespace will be removed from the
     * namespace list.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array namespace
     * @return void
     */
    public function removeNamespace($namespace)
    {
        
        foreach ($this->getNamespaces() as $ns) {
        	if ($ns['prefix'] == $namespace['prefix']) {
        		unset($ns);
				break;
        	}
        }
        
    }

    /**
     * Gets the current namespaces list.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getNamespaces()
    {
        $returnValue = array();

        
        $returnValue = $this->namespaces;
        

        return (array) $returnValue;
    }

    /**
     * Sets the base namespace to which the RDFTranslationUnits belong to.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string base
     * @return void
     */
    public function setBase($base)
    {
        
        $this->base = $base;
        
    }

    /**
     * Gets the current base namespace to which the RDFTranslationUnits belong
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getBase()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->base;
        

        return (string) $returnValue;
    }

}

?>