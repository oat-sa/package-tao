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
 * Copyright (c) 2002-2008 (original work) 2014 Open Assessment Technologies SA
 * 
 */
namespace oat\generis\model\kernel\persistence\wrapper;

use oat\generis\model\data\RdfsInterface;
/**
 * Wraps the RdfsInterface in a Rdf interface
 * 
 * @author Joel Bout
 */
class RdfWrapper
    implements \oat\generis\model\data\RdfInterface
{
    /**
     * @var RdfsInterface
     */
    private $rdfsInterface;
    
    public function __construct(RdfsInterface $rdfsInterface) {
        $this->rdfsInterface = $rdfsInterface;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::get()
     */
    public function get($subject, $predicate) {
        throw new \common_Exception('Not implemented');
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::add()
     */
    public function add(\core_kernel_classes_Triple $triple) {
        $resource = new \core_kernel_classes_Resource($triple->subject);
        switch ($triple->object) {
        	case RDF_TYPE :
        	    $class = new \core_kernel_classes_Class($triple->object);
        	    return $this->rdfsInterface->getResourceImplementation()->setType($resource, $class);
        	    break;
        	    
    	    case RDFS_RANGE :
        	    $class = new \core_kernel_classes_Class($triple->object);
        	    return $this->rdfsInterface->getPropertyImplementation()->setRange($resource, $class);
        	    break;
        	    
        	case PROPERTY_MULTIPLE :
        	    $value = $triple->object == GENERIS_TRUE;
        	    return $this->rdfsInterface->getPropertyImplementation()->setMultiple($resource, $value);
        	    break;
        	    
    	    case PROPERTY_IS_LG_DEPENDENT :
        	    $value = $triple->object == GENERIS_TRUE;
        	    return $this->rdfsInterface->getPropertyImplementation()->isLgDependent($resource, $value);
        	    break;
    	        
	        case RDFS_DOMAIN :
	        default:
	            $property = new \core_kernel_classes_Property($triple->predicate);
	            if (empty($triple->lg)) {
	                return $this->rdfsInterface->getResourceImplementation()->setPropertyValue($resource, $property, $triple->object);
	            }  else {
	                return $this->rdfsInterface->getResourceImplementation()->setPropertyValueByLg($resource, $property, $triple->object, $triple->lg);
	            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::remove()
     */
    public function remove(\core_kernel_classes_Triple $triple) {
        throw new \common_Exception('Not implemented');
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::search()
     */
    public function search($predicate, $object) {
        throw new \common_Exception('Not implemented');
    }
    
    public function getIterator() {
        throw new \common_Exception('Not implemented');
    }
}