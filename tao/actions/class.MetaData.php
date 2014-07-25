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
 */

/**
 * the module MetaData allows reading and saving metadata
 * associated to a resource 
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 */
class tao_actions_MetaData extends tao_actions_CommonModule {
	
	protected function getCurrentInstance()
	{
	    $uri = null;
	    if ($this->hasRequestParameter('uri')) {
		  $uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
	    } 
	    if (empty($uri) && $this->hasRequestParameter('classUri')) {
	        $uri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
	    }
		if(is_null($uri) || empty($uri)){
			throw new common_exception_MissingParameter("uri", __CLASS__);
		}
		return new core_kernel_classes_Resource($uri);
	}
	
	/**
	 * get the meta data of the selected resource
	 * Display the metadata. 
	 * @return void
	 */
	public function getMetaData()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$this->setData('metadata', false);
		$instance = $this->getCurrentInstance();
		$commentUris = $instance->getPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_RESOURCE_COMMENT));
		$commentData = array();
		foreach ($commentUris as $uri) {
			$comment = new core_kernel_classes_Resource($uri);
			$props = $comment->getPropertiesValues(array(
				RDF_VALUE, PROPERTY_COMMENT_AUTHOR, PROPERTY_COMMENT_TIMESTAMP
			));
			$author = current($props[PROPERTY_COMMENT_AUTHOR]);
			$date = current($props[PROPERTY_COMMENT_TIMESTAMP]);
			$text = (string)current($props[RDF_VALUE]);
			$commentData[(string)$date] = array(
				'author' => $author->getLabel(),
				'date' => tao_helpers_Date::displayeDate((string)$date),
				'text' => _dh($text)
			);
			
			ksort($commentData);
		}
		
		
		
		$this->setData('comments',	$commentData);
		$this->setData('uri',		$instance->getUri());
		$this->setData('metadata',	true);
		
		$this->setView('form/metadata.tpl', 'tao');
	}
	
	/**
	 * save the comment field of the selected resource
	 * @return json response {saved: true, comment: text of the comment to refresh it}
	 */
	public function saveMetaData()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$instance = $this->getCurrentInstance();
		$commentClass = new core_kernel_classes_Class(CLASS_GENERIS_COMMENT);
		$time = time();
		$author = new core_kernel_classes_Resource(core_kernel_classes_Session::singleton()->getUserUri());
		
		$comment = $commentClass->createInstanceWithProperties(array(
			RDF_VALUE					=> $this->getRequestParameter('comment'),
			PROPERTY_COMMENT_AUTHOR		=> $author->getUri(),
			PROPERTY_COMMENT_TIMESTAMP	=> $time
		));

		$instance->setPropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_RESOURCE_COMMENT), $comment);
		echo json_encode(array(
			'saved' 	=> true,
		    'author'    => $author->getLabel(),
		    'date'      => tao_helpers_Date::displayeDate((string)$time),
			'text' 	    => $this->getRequestParameter('comment')
		));
	}

}
?>
