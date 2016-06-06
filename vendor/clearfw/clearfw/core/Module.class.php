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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2014      (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * Module class
 * TODO Module class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Module extends Actions implements IFlowControl, IViewable
{
	/**
	 * @var Renderer
	 */
	protected $renderer;
	
	public function forward($action, $controller = null, $extension = null, $params = array())
	{
		$flowController = new FlowController();
		$flowController->forward($action, $controller, $extension, $params);
	}
	
	public function forwardUrl($url)
	{
		$flowController = new FlowController();
		$flowController->forwardUrl($url);
	}

	public function redirect($url)
	{
		$flowController = new FlowController();
		$flowController->redirect($url);
	}
	
	public function getRenderer() {
		if (!isset($this->renderer)) {
			$this->renderer = new Renderer();
		}
		return $this->renderer;
	}
	
	public function setView($identifier)
	{
		$this->getRenderer()->setTemplate($identifier);
	}
	
	public function setData($key, $value)
	{
		$this->getRenderer()->setData($key, $value);
	}
	
	public function hasView() {
		return isset($this->renderer) && $this->renderer->hasTemplate();
	}
	
}
