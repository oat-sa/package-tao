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
 * Represents a generic service providing programming conveniences
 * to write basic REST webservices for the TAO Installer.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
abstract class tao_install_services_Service{
    
    /*
     * The HTTP Status returned by the Service.
     */
    private $status;
    
    /**
     * The result returned by the Service. It must be an instance
     * of tao_install_Services_Data.
     */
    private $result = null;
    
    /**
     * The request Data handled by the Service. It must be an instance
     * of tao_install_services_Data.
     */
    private $data = null;
    
    /**
     * Creates a new instance of tao_install_Services_Service with
     * its associated request Data.
     * 
     * @param tao_install_services_Data $data The request Data.
     */
    public function __construct(tao_install_services_Data $data){
        // By default, status is OK.
        $this->setStatus(200);
        $this->setData($data);
        $this->checkData();
    }
    
    /**
     * Gets the current HTTP status for the current request handled by the Service.
     * @return int An HTTP code.
     */
    public function getStatus(){
        return $this->status;
    }
    
    /**
     * Sets the current HTTP status for the current request handled by the Service.
     * @param int $status An HTTP Code.
     * @return void
     */
    protected function setStatus($status){
        $this->status = $status;
    }
    
    /**
     * Gets the request Data handled by the Service.
     * @return tao_install_services_Data The Data handled by the Service.
     */
    protected function getData(){
        return $this->data;
    }
    
    /**
     * Sets the request Data handled by the Service.
     * @param tao_install_Services_Data $data The request Data that must be handled by the service.
     * @return void
     */
    protected function setData(tao_install_services_Data $data){
        $this->data = $data;
    }
    
    /**
     * Gets the request result that has to be returned by the Service.
     * @return tao_install_Services_Data A request result.
     */
    public function getResult(){
        return $this->result;
    }
    
    /**
     * Sets the request result that has to be returned by the Service.
     * @param tao_install_services_Data $data The Data that represents the Service result.
     * @return void
     */
    protected function setResult(tao_install_services_Data $result){
        $this->result = $result;
    }
    
    /**
     * Provides the current request method (get,post,put,delete) in lowercase.
     * @return string the current request method.
     */
    protected static function getRequestMethod(){
    	return strtolower($_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Contains the business logic of the Service. Any implementation of tao_install_Services_Service
     * must implement this method and should take care of setting the tao_install_Services_Service::result
     * in their implementation.
     * @return tao_install_services_Data The resulting Data.
     */
    protected abstract function execute();
    
    /**
     * Contains the logic of checking the input data for a given service implementation.
     */
    protected function checkData(){
    	return;
    }
}
?>