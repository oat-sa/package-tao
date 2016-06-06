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
namespace oat\taoWorkspace\model\generis;

use oat\generis\model\data\RdfInterface;

/**
 * Implementation of the RDFS interface for the smooth sql driver
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class WrapperRdf
    implements RdfInterface
{
    private $inner;
    
    private $model;
    
    public function __construct(RdfInterface $inner, WrapperModel $model)
    {
        $this->inner = $inner;
        $this->model = $model;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::add()
     */
    public function add(\core_kernel_classes_Triple $triple)
    {
        if (!in_array($triple->modelid, $this->model->getReadableModels())) {
            $this->model->addReadableModel($triple->modelid);
        }
        $this->inner->add($triple);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::get()
     */
    public function get($subject, $predicate)
    {
        return $this->inner->get($subject, $predicate);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::remove()
     */
    public function remove(\core_kernel_classes_Triple $triple)
    {
        $this->inner->remove($triple);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::search()
     */
    public function search($predicate, $object)
    {
        return $this->inner->search($predicate, $object);
    }
    
    /**
     * (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator() {
        return $this->inner->getIterator();
    }
}