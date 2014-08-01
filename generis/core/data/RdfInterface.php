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
namespace oat\generis\model\data;

/**
 * Rdf interface to access the ontology
 * This is an experimental interface that has not been implemented yet
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
interface RdfDriver
{
    /**
     * Adds a triple to the ontology
     * 
     * @param string $subject
     * @param string $predicate
     * @param string $value$object
     */
    public function set($subject, $predicate, $object);
    
    /**
     * Returns an array of the objects of all triples with the given subject, predicate
     * 
     * @param string $subject
     * @param string $predicate
     * @return array
     */
    public function get($subject, $predicate);
    
    /**
     * Removes the triple with the given subject, predicate, object
     * 
     * @param string $subject
     * @param string $predicate
     * @param string $object
     */
    public function remove($subject, $predicate, $object);
    
    /**
     * Returns an array of the subjects of all triples with the given predicate, object
     * 
     * @param string $predicate
     * @param string $object
     * @return array
     */
    public function search($predicate, $object);
}