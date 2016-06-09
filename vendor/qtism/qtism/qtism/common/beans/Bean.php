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

use \InvalidArgumentException;
use \ReflectionClass;
use \ReflectionObject;
use \ReflectionMethod;
use \ReflectionProperty;

/**
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Bean {
    
    /**
     * The string literal corresponding to a bean property annotation.
     * 
     * @var string
     */
    const ANNOTATION_PROPERTY = "@qtism-bean-property";
    
    /**
     * The object to be wrapped as a bean as a PHP ReflectionObject.
     * 
     * @var ReflectionClass
     */
    private $class;
    
    /**
     * Create a new Bean object.
     * 
     * @param mixed $object The object to be wrapped as a Bean.
     * @param boolean $strict Whether the given $object must be a strict Bean.
     * @param string $asInstanceOf The name of the class that should be used for reflection.
     * @throws InvalidArgumentException If $object is not an object.
     */
    public function __construct($object, $strict = false, $asInstanceOf = '') {
        if (is_object($object) === true) {
            if (empty($asInstanceOf) === true) {
                $this->setClass(new ReflectionClass($object));
            }
            else {
                $this->setClass(new ReflectionClass($asInstanceOf));
            }
            
            if ($strict === true) {
                try {
                    $this->validateStrictBean();
                }
                catch (BeanException $e) {
                    $msg = "The given object is not a strict bean.";
                    throw new BeanException($msg, BeanException::NOT_STRICT, $e);
                }
            }
        }
        else {
            $msg = "The given 'object' argument is not an object.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Set the object to be wrapped Bean as a PHP ReflectionClass.
     * 
     * @param ReflectionClass $class A ReflectionClass object. 
     */
    protected function setClass(ReflectionClass $class) {
        $this->class = $class;
    }
    
    /**
     * Get the object to be wrapped as a Bean as a PHP ReflectionClass.
     * 
     * @return ReflectionClass A ReflectionClass object.
     */
    protected function getClass() {
        return $this->class;
    }
    
    /**
     * Get the getter related to the property with name $propertyName.
     * 
     * @param string|BeanProperty $property The name of the property/the BeanProperty object the getter is related to.
     * @return BeanMethod A BeanMethod object.
     * @throws BeanException If no such valid bean property or getter exists for the bean.
     * @throws InvalidArgumentException If $property is not a string nor a Bean
     */
    public function getGetter($property) {
        
        if (is_string($property) === true) {
            $propertyName = $property;
        }
        else if ($property instanceof BeanProperty) {
            $propertyName = $property->getName();
        }
        else {
            $msg = "The 'property' argument must be a string or a BeanProperty object.";
            throw new InvalidArgumentException($msg);
        }
        
        if ($this->hasProperty($propertyName) === false) {
            $msg = "The bean has no '${propertyName}' property.";
            throw new BeanException($msg, BeanException::NO_METHOD);
        }
        
        if (($getterName = $this->hasGetter($propertyName)) === false) {
            $msg = "The bean has no public getter for a '${propertyName}' property.";
            throw new BeanException($msg, BeanException::NO_METHOD);
        }
        
        return new BeanMethod($this->getClass()->getName(), $getterName);
    }
    
    /**
     * Whether the bean has a valid getter for a property with name $propertyName.
     * A getter is considered to be valid if:
     * 
     * * Its name is 'get' + ucfirst($propertyName).
     * * Its visibility is public.
     * * A valid bean property exists for $propertyName.
     * 
     * @param string|BeanProperty $property The name of the property/the BeanProperty the getter is related to.
     * @return false|string False if not found or the final chosen name for the getter.
     * @throws InvalidArgumentException If $property is not a string nor a BeanProperty object.
     */
    public function hasGetter($property) {
        
        if (is_string($property) === true) {
            $propertyName = $property;
        }
        else if ($property instanceof BeanProperty) {
            $propertyName = $property->getName();
        }
        else {
            $msg = "The 'property' argument must be a string or a BeanProperty object.";
            throw new InvalidArgumentException($msg);
        }
        
        $getterNames = self::getPossibleGetterNames($propertyName);
        $hasGetter = false;
        
        foreach ($getterNames as $possibleGetterName) {
            if ($this->getClass()->hasMethod($possibleGetterName) === true && $this->hasProperty($propertyName) === true) {
                if ($this->getClass()->getMethod($possibleGetterName)->isPublic()) {
                    $hasGetter = $possibleGetterName;
                    break;
                }
            }
        }
        
        return $hasGetter;
    }
    
    /**
     * Get the valid bean getters of the bean.
     * 
     * @return BeanMethodCollection A collection of BeanMethod objects.
     */
    public function getGetters($excludeConstructor = false) {
        $methods = new BeanMethodCollection();
        
        foreach ($this->getProperties() as $prop) {
            if (($getterName = $this->hasGetter($prop->getName())) !== false) {
                
                if ($excludeConstructor === false || $this->hasConstructorParameter($prop->getName()) === false) {
                    $methods[] = new BeanMethod($this->getClass()->getName(), $getterName);
                }
            }
        }
        
        return $methods;
    }
    
    /**
     * Get the setter related to the property with name $propertyName.
     * 
     * @param string|BeanProperty $property The name of the property/The BeanProperty object the setter is related to.
     * @return BeanMethod A BeanMethod object.
     * @throws BeanException If no such valid bean property or setter exists for the bean.
     * @throws InvalidArgumentException If $property is not a string nor a BeanProperty object.
     */
    public function getSetter($property) {
        
        if (is_string($property) === true) {
            $propertyName = $property;
        }
        else if ($property instanceof BeanProperty) {
            $propertyName = $property->getName();
        }
        else {
            $msg = "The 'property' argument must be a string or a BeanProperty object.";
            throw new InvalidArgumentException($msg);
        }
        
        if ($this->hasProperty($propertyName) === false) {
            $msg = "The bean has no '${propertyName}' property.";
            throw new BeanException($msg, BeanException::NO_METHOD);
        }
        
        if ($this->hasSetter($propertyName) === false) {
            $msg = "The bean has no public setter for a '${propertyName}' property.";
            throw new BeanException($msg, BeanException::NO_METHOD);
        }
        
        return new BeanMethod($this->getClass()->getName(), 'set' . ucfirst($propertyName));
    }
    
    /**
     * Whether the bean has a valid setter for a property with name $propertyName.
     * A setter is considered to be valid if:
     * 
     * * Its name is 'set' + ucfirst($propertyName).
     * * Its visibility is public.
     * * A valid bean property exists for $propertyName.
     * 
     * @param string|BeanProperty $property The name of the property/the BeanProperty object related to the setter to be checked.
     * @return boolean
     */
    public function hasSetter($property) {
        
        if (is_string($property) === true) {
            $propertyName = $property;
        }
        else if ($property instanceof BeanProperty) {
            $propertyName = $property->getName();
        }
        else {
            $msg = "The 'property' argument must be a string or a BeanProperty object.";
            throw new InvalidArgumentException($msg);
        }
        
        $setterName = 'set' . ucfirst($propertyName);
        $hasSetter = false;
        
        if ($this->getClass()->hasMethod($setterName) === true && $this->hasProperty($propertyName) === true) {
            $hasSetter = $this->getClass()->getMethod($setterName)->isPublic();
        }
        
        return $hasSetter;
    }
    
    /**
     * Get the valid setters of this bean.
     * 
     * @return BeanMethodCollection A collection of BeanMethod objects.
     */
    public function getSetters($excludeConstructor = false) {
        $methods = new BeanMethodCollection();
        
        foreach ($this->getProperties() as $prop) {
            if ($this->hasSetter($prop->getName()) === true) {
                
                if ($excludeConstructor === false || $this->hasConstructorParameter($prop->getName()) === false) {
                    $methods[] = new BeanMethod($this->getClass()->getName(), 'set' . ucfirst($prop->getName()));
                }
            }
        }
        
        return $methods;
    }
    
    /**
     * Whether the bean has a bean property named $propertyName. The bean is considered
     * to have a given bean property if:
     * 
     * * The given $propertyName corresponds to a propert of the class.
     * * The property is annotated with @qtism-bean-property.
     * 
     * @param string $propertyName The name of the class property to check.
     * @return boolean
     */
    public function hasProperty($propertyName) {
       return $this->isPropertyAnnotated($propertyName);
    }
    
    /**
     * Get a bean property with name $propertyName.
     * 
     * @param string $propertyName The name of the bean property.
     * @return BeanProperty A BeanProperty object.
     */
    public function getProperty($propertyName) {
        $className = $className = $this->getClass()->getName();
        
        if ($this->hasProperty($propertyName) === true) {

            try {
                return new BeanProperty($className, $propertyName);
            }
            catch (BeanException $e) {
                $msg = "The bean property with name '${propertyName}' in class '${className}' could not be retrieved.";
                throw new BeanException($msg, BeanException::NO_PROPERTY, $e);
            }
        }
        else {
            $msg = "No bean property with name '${propertyName}' in class '${className}'.";
            throw new BeanException($msg, BeanException::NO_PROPERTY);
        }
    }
    
    /**
     * Get the bean properties. Only valid annotated bean properties will be returned.
     * 
     * @return BeanPropertyCollection A collection of BeanProperty objects ordered by apparition in source code.
     */
    public function getProperties() {
        $properties = new BeanPropertyCollection();
        
        // Properties already taken into account accross the hierarchy.
        $registeredProperties = array();
        $target = $this->getClass();
        
        while ($target) {
            
            foreach ($target->getProperties() as $prop) {
                
                $propName = $prop->getName();
                
                if ($this->isPropertyAnnotated($propName) === true && in_array($propName, $registeredProperties) === false) {
                    $properties[] = new BeanProperty($target->getName(), $propName);
                    $registeredProperties[] = $propName;
                }
            }
            
            $target = $target->getParentClass();
        }
        
        return $properties;
    }
    
    /**
     * Get the bean getters related to the parameters of the bean's constructor.
     * 
     * @return BeanMethodCollection A collection of BeanMethod objects.
     */
    public function getConstructorGetters() {
        $getters = new BeanMethodCollection();
        
        foreach ($this->getConstructorParameters() as $param) {
            $getters[] = $this->getGetter($param->getName());
        }
        
        return $getters;
    }
    
    /**
     * Get the bean setters related to the parameters of the bean's constructor.
     *
     * @return BeanMethodCollection A collection of BeanMethod objects.
     */
    public function getConstructorSetters() {
        $setters = new BeanMethodCollection();
    
        foreach ($this->getConstructorParameters() as $param) {
            $setters[] = $this->getSetter($param->getName());
        }
    
        return $setters;
    }
    
    /**
     * Get the constructor parameters of the bean. Only parameters that have the same name
     * than a valid bean property will be returned.
     * 
     */
    public function getConstructorParameters() {
        $parameters = new BeanParameterCollection();
        
        if (($ctor = $this->getClass()->getConstructor()) !== null) {
            
            
            foreach ($ctor->getParameters() as $param) {
                if ($this->hasProperty($param->getName()) === true) {
                    $parameters[] = new BeanParameter($this->getClass()->getName(), '__construct', $param->getName());
                }
            }
        }
        
        return $parameters;
    }
    
    /**
     * Whether the bean has a constructor parameter $parameterName which is related
     * to a valid bean property.
     * 
     * @param string $parameterName The name of the parameter.
     * @return boolean
     */
    public function hasConstructorParameter($parameterName) {
        $hasConstructorParameter = false;
        
        if (($ctor = $this->getClass()->getConstructor()) !== null) {
            
            foreach ($ctor->getParameters() as $param) {
                if ($param->getName() === $parameterName && $this->hasProperty($param->getName()) === true) {
                    $hasConstructorParameter = true;
                    break;
                }
            }
        }
        
        return $hasConstructorParameter;
    }
    
    /**
     * Whether a given property is annotated with the appropriate bean annotation.
     * 
     * @param string $propertyName The name of the property.
     * @return boolean
     */
    protected function isPropertyAnnotated($propertyName) {
        $target = $this->getClass();
        $isAnnotated = false;
        
        while ($target) {
            
            if ($target->hasProperty($propertyName)) {
                $comment = $target->getProperty($propertyName)->getDocComment();
                if (empty($comment) === false) {
                    $isAnnotated = mb_strpos($comment, self::ANNOTATION_PROPERTY, 0, 'UTF-8') !== false;
                    
                    if ($isAnnotated === true) {
                        break;
                    }
                    
                }
            }
            
            $target = $target->getParentClass();
        }
        
        return $isAnnotated;
    }
    
    /**
     * Contains the internal logic of bean validation. Throws exceptions
     * to know why it's not a valid bean.
     * 
     * @throws BeanException
     */
    protected function validateStrictBean() {
        /*
         * 1st rule to respect:
         * All the constructor's parameters must be bound
         * to a valid annotated property, and the appropriate
         * getter and setter. (This implies the bean has a constructor)
         */
        if ($this->getClass()->hasMethod('__construct') === true) {
            $params = $this->getClass()->getMethod('__construct')->getParameters();
            $class = $this->getClass()->getName();
            
            foreach ($params as $param) {
                $name = $param->getName();
                
                if ($this->hasProperty($name) === false) {
                    $msg = "The constructor parameter '${name}' of class '${class}' has no related bean property.";
                    throw new BeanException($msg, BeanException::NO_PROPERTY);
                }
                else if ($this->hasGetter($name) === false) {
                    $msg = "The constructor parameter '${name}' of class '${class}' has no related bean getter.";
                    throw new BeanException($msg, BeanException::NO_METHOD);
                }
                else if ($this->hasSetter($name) === false) {
                    $msg = "The construct parameter '${name}' of class '${class}' has no related bean setter.";
                    throw new BeanException($msg, BeanException::NO_METHOD);
                }
            }
        }
        
        /*
         * 2nd rule to respect is that any property annotated as a bean property
         * must have the appropriate getter and setter.
         */
        foreach ($this->getClass()->getProperties() as $prop) {
            $name = $prop->getName();
            
            if ($this->hasProperty($name) === true) {
                // Annotated property found.
                if ($this->hasGetter($name) === false) {
                    $msg = "The bean property '${name}' has no related bean getter.";
                    throw new BeanException($msg, BeanException::NO_METHOD);
                }
                else if ($this->hasSetter($name) === false) {
                    $msg = "The bean property '${name}' has no related bean setter.";
                    throw new BeanException($msg, BeanException::NO_METHOD);
                }
            }
        }
    }
    
    /**
     * Get the possible names a bean getter can take for a given $propertyName.
     * 
     * Imagine a boolean bean class property named 'formatOutput'. By convention,
     * its getter can be named 'getFormatOutput', 'isFormatOutput', 'mustFormatOutput'
     * or 'doesFormatOutput'.
     * 
     * @param string $propertyName The name of the property.
     * @return array An array of possible getter method names for a given $propertyName.
     */
    protected static function getPossibleGetterNames($propertyName) {
        $ucPropName = ucfirst($propertyName);
        return array(
            'get' . $ucPropName,
            'is' . $ucPropName,
            'must' . $ucPropName,
            'does' . $ucPropName
        );
    }
}
