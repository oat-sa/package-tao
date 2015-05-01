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

use core_kernel_classes_Triple;

/**
 * Rdf interface to access the ontology
 * This is an experimental interface that has not been implemented yet
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
interface RdfInterface extends \IteratorAggregate
{
    /**
     * Returns an array of the triples with the given subject, predicate
     * 
     * @param string $subject
     * @param string $predicate
     * @return array
     */
    public function get($subject, $predicate);
    
    /**
     * Adds a triple to the model
     * 
     * @param \core_kernel_classes_Triple $triple
     */
    public function add(\core_kernel_classes_Triple $triple);
    
    /**
     * Removes the triple
     * 
     * @param \core_kernel_classes_Triple $triple
     */
    public function remove(\core_kernel_classes_Triple $triple);
    
    /**
     * Returns an array of the triples with the given predicate, object
     * 
     * @param string $predicate
     * @param string $object
     * @return array
     */
    public function search($predicate, $object);
}