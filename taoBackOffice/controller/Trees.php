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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2015 (update and modification) Open Assessment Technologies SA;
 * 
 */

namespace oat\taoBackOffice\controller;

use core_kernel_classes_Class;
use oat\taoBackOffice\model\tree\TreeService;
use oat\tao\model\menu\MenuService;
use oat\tao\model\accessControl\ActionResolver;

/**
 * This controller provide the actions to manage the lists of data
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoBackOffice
 * 
 *
 */
class Trees extends \tao_actions_RdfController {

    /**
     * @return TreeService
     */
	public function getClassService()
	{
	    return TreeService::singleton();
	}
	
	/**
	 * 
	 * @return \core_kernel_classes_Class
	 */
	protected function getRootClass()
	{
	    return $this->getClassService()->getRootClass();
	}

	/**
	 * Visualises the tree
	 */
	public function getTree()
	{
	    $tree = new core_kernel_classes_Class($this->getRequestParameter('uri'));
		$struct = $this->getClassService()->getFlatStructure(
			$tree,
			function ( $label ) {
				return wordwrap($label, 30, "\n");
			}
		);
		$this->returnJson($struct);

	}

	/**
	 * Renders a tree
	 * @requiresRight id READ
	 */
	public function viewTree(){

		$this->setData('id', $this->getRequestParameter('id'));

		$this->setView('Trees/viewTree.tpl');

	}

	/**
	 * Returns an empty view 
	 */
	public function dummy()
	{
	}

	/**
	 * Populates the Tree of Trees
	 * 
	 * @requiresRight classUri READ 
	 */
	public function getTreeData()
	{
	    $data = array(
	        'data' => __("Trees"),
	        'type' => 'class',
	        'attributes' => array(
	            'id' => \tao_helpers_Uri::encode($this->getRootClass()->getUri()),
	            'class' => 'node-class',
	            'data-uri' => $this->getRootClass()->getUri()
	        ),

	    );

		$sublasses = $this->getRootClass()->getSubClasses(false);

		if (count( $sublasses )) {
			$data['children'] = array();
		}

	    foreach ( $sublasses as $class) {
	        $data['children'][] = array(
	            'data' => $class->getLabel(),
	            'type' => 'instance',
	            'attributes' => array(
	                'id' => \tao_helpers_Uri::encode($class->getUri()),
	                'class' => 'node-instance',
	                'data-uri' => $class->getUri()
	            )
	        );
	    }
	    
	    $data = $this->addPermissions($data);
	
	    $this->returnJson($data);
	}
	
}
