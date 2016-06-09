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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

/**
 * Iterates over a class(es) and its subclasses 
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class core_kernel_classes_ClassIterator implements \Iterator
{	
    /**
     * List of classes whose subclasses have not yet been exploded
     * 
     * @var array
     */
    private $todoClasses = array();
    
    private $classes = array();

    private $currentId= -1;
    
    /**
     * Constructor of the iterator expecting a class or classes as argument
     * 
     * @param mixed $classes array/instance of class(es) to iterate over
     */
    public function __construct($classes) {
        $classes = is_array($classes) ? $classes : array($classes);
        foreach ($classes as $class) {
            $this->todoClasses[] = (is_object($class) && $class instanceof core_kernel_classes_Class) ? $class->getUri() : $class;
        }
        $this->rewind();
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    function rewind() {
        $this->currentId = -1;
        $this->next();
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    function current() {
        return new \core_kernel_classes_Class($this->classes[$this->currentId]);
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    function key() {
        return $this->currentId;
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    function next() {
        $this->currentId++;
        if (!isset($this->classes[$this->currentId]) && !empty($this->todoClasses)) {
            $newUri = array_shift($this->todoClasses);
            $this->classes[] = $newUri;
            $class = new \core_kernel_classes_Class($newUri);
            foreach ($class->getSubClasses(false) as $subClass) {
                if (!in_array($subClass->getUri(), $this->classes) && !in_array($subClass->getUri(), $this->todoClasses)) {
                    $this->todoClasses[] = $subClass->getUri();
                }
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    function valid() {
        return isset($this->classes[$this->currentId]);
    }
}
