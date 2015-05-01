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
 * Short description of class tao_helpers_translation_RDFTranslationUnit
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_translation_RDFTranslationUnit
    extends tao_helpers_translation_TranslationUnit
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute subject
     *
     * @access private
     * @var string
     */
    private $subject = '';

    /**
     * Short description of attribute predicate
     *
     * @access private
     * @var string
     */
    private $predicate = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getSubject()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->subject;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getPredicate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getPredicate()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->predicate;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setSubject
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string subject
     * @return mixed
     */
    public function setSubject($subject)
    {
        
        $this->subject = $subject;
        $this->addAnnotation('subject', $subject);
        
    }

    /**
     * Short description of method setPredicate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string predicate
     * @return mixed
     */
    public function setPredicate($predicate)
    {
        
        $this->predicate = $predicate;
        $this->addAnnotation('predicate', $predicate);
        
    }

    /**
     * Checks whether or not a given RDFTranslationUnit has the same subject
     * value as the current instance.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  RDFTranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitSubject( tao_helpers_translation_RDFTranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->getSubject() == $translationUnit->getSubject();
        

        return (bool) $returnValue;
    }

    /**
     * Checks whether or not a given RDFTranslationUnit has the same predicate
     * value as the current instance.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  RDFTranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitPredicate( tao_helpers_translation_RDFTranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->getPredicate() == $translationUnit->getPredicate();
        

        return (bool) $returnValue;
    }

    /**
     * Checks wether or not that the current translation unit has the same
     * than another one. For RDFTranslationUnits, we consider that two
     * units have the same source if their source, subject, predicate and target
     * are identical.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit A translation unit to compare.
     * @return boolean
     */
    public function hasSameTranslationUnitSource( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->hasSameTranslationUnitPredicate($translationUnit) &&
                       $this->hasSameTranslationUnitSubject($translationUnit) &&
                       $this->hasSameTranslationUnitTargetLanguage($translationUnit);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method setSource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string source
     * @return mixed
     */
    public function setSource($source)
    {
        
        parent::setSource($source);
        $this->addAnnotation('source', $source)
;        
    }

}

?>