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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */
namespace oat\taoQtiItem\model\event;

use oat\oatbox\event\Event;

class ItemImported implements Event
{
    
    /**
     * The Qti Item model object
     * @var \oat\taoQtiItem\model\qti\Item
     */
    private $item;

    /**
     * The Rdf Qti Item
     * @var type
     */
    private $rdfItem;


    /**
     * Create an instance of ItemImported event
     * 
     * @param \core_kernel_classes_Resource $rdfItem
     * @param \oat\taoQtiItem\model\qti\Item $item
     */
    public function __construct(\core_kernel_classes_Resource $rdfItem, \oat\taoQtiItem\model\qti\Item $item)
    {
        $this->item = $item;
        $this->rdfItem = $rdfItem;
    }

    /**
     * @see \oat\oatbox\event\Event::getName()
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * @return \oat\taoQtiItem\model\qti\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return \core_kernel_classes_Resource
     */
    public function getRdfItem()
    {
        return $this->rdfItem;
    }

}