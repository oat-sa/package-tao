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

use oat\generis\model\data\RdfsInterface;

/**
 * Implementation of the RDFS interface for the smooth sql driver
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class WrapperRdfs
    implements RdfsInterface
{
    /**
     * @var \core_kernel_persistence_ResourceInterface
     */
    private $resource;
    
    /**
     * @var \core_kernel_persistence_ClassInterface
     */
    private $class;
    
    /**
     * @var \core_kernel_persistence_PropertyInterface
     */
    private $property;
    
    
    public function __construct(RdfsInterface $inner, RdfsInterface $workSpaceRdfs) {
        $this->resource = new WrapperResource($inner->getResourceImplementation(), $workSpaceRdfs->getResourceImplementation());
        $this->class = $inner->getClassImplementation();
        $this->property = $inner->getPropertyImplementation();
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfsInterface::getClassImplementation()
     */
    public function getClassImplementation() {
        return $this->class;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfsInterface::getResourceImplementation()
     */
    public function getResourceImplementation() {
        return $this->resource;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfsInterface::getPropertyImplementation()
     */
    public function getPropertyImplementation() {
        return $this->property;
    }
}