<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\common\beans;

use \ReflectionProperty;
use \ReflectionException;

/**
 * Represents a Bean property. In other words, a class property
 * annotated with @qtism-bean-property.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BeanProperty {
    
    /**
     * The wrapped ReflectionProperty object.
     * 
     * @var ReflectionProperty
     */
    private $property;
    
    /**
     * Create a new BeanProperty object.
     * 
     * @param string $class The name of the class the property belongs to.
     * @param string $name The name of the property.
     * @throws BeanException If such a property does not exist or is not correctly annotated.
     */
    public function __construct($class, $name) {
        try {
            $this->setProperty(new ReflectionProperty($class, $name));
        }
        catch (ReflectionException $e) {
            $msg = "The class property with name '${name}' does not exist in class '${class}'.";
            throw new BeanException($msg, BeanException::NO_PROPERTY, $e);
        }
        catch (BeanException $e) {
            $msg = "The property with name '${name}' for class '${class}' is not annotated.";
            throw new BeanException($msg, BeanException::NO_PROPERTY, $e);
        }
    }
    
    /**
     * Get the name of the bean property.
     * 
     * @return string
     */
    public function getName() {
        return $this->getProperty()->getName();
    }
    
    /**
     * Set the wrapped ReflectionProperty object.
     * 
     * @param ReflectionProperty $property A ReflectionProperty object.
     * @throws BeanException If the given $property is not annotated with @qtism-bean-property.
     */
    protected function setProperty(ReflectionProperty $property) {
        if (mb_strpos($property->getDocComment(), Bean::ANNOTATION_PROPERTY, 0, 'UTF-8') !== false) {
            $this->property = $property;
        }
        else {
            $msg = "The property must be annotated with '@qtism-bean-property'.";
            throw new BeanException($msg, BeanException::NO_ANNOTATION);
        }
    }
    
    /**
     * Get the wrapped ReflectionProperty.
     * 
     * @return ReflectionProperty A ReflectionProperty object.
     */
    public function getProperty() {
        return $this->property;
    }
}