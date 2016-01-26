<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 *
 * Factory to prepare the ontology data for the
 * javascript generis tree
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
namespace oat\tao\model;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\tao\helpers\TreeHelper;
use tao_helpers_Uri;

class GenerisTreeFactory
{
	/**
	 * All instances of those classes loaded, independent of current limit ( Contain uris only )
	 * @var array
	 */
	private $browsableTypes = array();
	/**
	 * @var int
	 */
	private $limit;
	/**
	 * @var int
	 */
	private $offset;
	/**
	 * @var array
	 */
	private $openNodes = array();
	/**
	 * @var bool
	 */
	private $showResources;

	/**
	 * @param boolean $showResources
	 * @param array $openNodes
	 * @param int $limit
	 * @param int $offset
	 * @param array $resourceUrisToShow All siblings of this resources will be loaded, independent of current limit
	 */
	public function __construct($showResources, array $openNodes = array(), $limit = 10, $offset = 0, array $resourceUrisToShow = array())
	{
		$this->limit          = (int) $limit;
		$this->offset         = (int) $offset;
		$this->openNodes      = $openNodes;
		$this->showResources  = $showResources;

		$types = array();
		foreach ($resourceUrisToShow as $uri) {
			$resource = new core_kernel_classes_Resource($uri);
			$types[]  = $resource->getTypes();
		}

		if ($types) {
			$this->browsableTypes = array_keys(call_user_func_array('array_merge', $types));
		}
	}

	/**
	 * builds the data for a generis tree
	 * @param core_kernel_classes_Class $class
	 * @return array
	 */
    public function buildTree(core_kernel_classes_Class $class) {
	    return $this->classToNode($class, null);
    }

	/**
	 * Builds a class node including it's content
	 *
	 * @param core_kernel_classes_Class $class
	 * @param core_kernel_classes_Class $parent
	 *
	 * @return array
	 */
    private function classToNode(core_kernel_classes_Class $class, core_kernel_classes_Class $parent = null) {
    	$label = $class->getLabel();
        $label = empty($label) ? __('no label') : $label;
        $returnValue = $this->buildClassNode($class, $parent);

        $instancesCount = (int) $class->countInstances();
        
        // allow the class to be opened if it contains either instances or subclasses
        if ($instancesCount > 0 || count($class->getSubClasses(false)) > 0) {
	        if (in_array($class->getUri(), $this->openNodes)) {
                    $returnValue['state']	= 'open';

		            $returnValue['children'] = $this->buildChildNodes($class);

            } else {
                    $returnValue['state']	= 'closed';
            }

            // only show the resources count if we allow resources to be viewed
	        if ($this->showResources) {
                $returnValue['count'] = $instancesCount;
            }
        }
        return $returnValue;
    }

	/**
	 * Builds the content of a class node including it's content
	 *
	 * @param core_kernel_classes_Class $class
	 *
	 * @return array
	 */
    private function buildChildNodes(core_kernel_classes_Class $class) {
    	$childs = array();
    	// subclasses
		foreach ($class->getSubClasses(false) as $subclass) {
			$childs[] = $this->classToNode($subclass, $class);
		}
		// resources
	    if ($this->showResources) {

		    $limit = $this->limit;

		    if (in_array($class->getUri(), $this->browsableTypes)) {
			    $limit = 0;
		    }

		    $searchResult = $class->searchInstances(array(), array(
				'limit'		=> $limit,
				'offset'	=> $this->offset,
				'recursive'	=> false
			));
			
			foreach ($searchResult as $instance){
				$childs[] = TreeHelper::buildResourceNode($instance, $class);
			}
		}
		return $childs;
    }

	/**
	 * generis tree representation of a class node
	 * without it's content
	 *
	 * @param core_kernel_classes_Class $class
	 * @param core_kernel_classes_Class $parent
	 *
	 * @return array
	 */
    private function buildClassNode(core_kernel_classes_Class $class, core_kernel_classes_Class $parent = null) {
    	$label = $class->getLabel();
		$label = empty($label) ? __('no label') : $label;
		return array(
			'data' 	=> _dh($label),
			'type'	=> 'class',
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($class->getUri()),
				'class' => 'node-class',
			    'data-uri' => $class->getUri(),
			    'data-classUri' => is_null($parent) ? null : $parent->getUri(),
			)
		);
    }

}
