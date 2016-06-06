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
 * Any Object that claims to be annotable should implement this interface.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
interface tao_helpers_translation_Annotable
{


    // --- OPERATIONS ---

    /**
     * Sets the collection of annotations bound to this Translation Object.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array annotations An associative array of annotations where keys are the annotation names and values are annotation values.
     * @return void
     */
    public function setAnnotations($annotations);

    /**
     * Returns an associative array that represents a collection of annotations
     * keys are annotation names and values annotation values.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAnnotations();

    /**
     * Adds an annotation with a given name and value. If value is not provided,
     * annotation will be taken into account as a flag.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the annotation to add.
     * @param  string value The value of the annotation to add.
     * @return void
     */
    public function addAnnotation($name, $value = '');

    /**
     * Removes an annotation for a given annotation name.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the annotation to remove.
     * @return void
     */
    public function removeAnnotation($name);

    /**
     * Get an annotation for a given annotation name. Returns an associative
     * where keys are 'name' and 'value'.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @return array
     */
    public function getAnnotation($name);

} /* end of interface tao_helpers_translation_Annotable */

?>