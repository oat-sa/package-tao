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
 */

namespace oat\qtiItemPci\controller;

use oat\taoQtiItem\controller\AbstractPortableElementManager;
use \tao_helpers_Http;
use \FileUploadException;
use oat\qtiItemPci\model\CreatorRegistry;
use oat\qtiItemPci\model\CreatorPackageParser;

class PciManager extends AbstractPortableElementManager
{
    
    protected function getCreatorRegistry(){
        return new CreatorRegistry();
    }
    
    /**
     * Returns the list of registered custom interactions and their data
     */
    public function getRegisteredImplementations(){

        $returnValue = array();

        $all = $this->registry->getRegisteredImplementations();

        foreach($all as $pci){
            $returnValue[$pci['typeIdentifier']] = $this->filterInteractionData($pci);
        }

        $this->returnJson($returnValue);
    }
    
    /**
     * Remove security sensitive data to be sent to the client
     * 
     * @param array $rawInteractionData
     * @return array
     */
    protected function filterInteractionData($rawInteractionData){
        
        unset($rawInteractionData['directory']);
        unset($rawInteractionData['registry']);
        return $rawInteractionData;
    }

    /**
     * Service to check if the uploaded file archive is a valid and non-existing one
     * 
     * JSON structure:
     * {
     *     "valid" : true/false (if is a valid package) 
     *     "exists" : true/false (if the package is valid, check if the typeIdentifier is already used in the registry)
     * }
     */
    public function verify(){

        $result = array(
            'valid' => false,
            'exists' => false
        );

        $file = tao_helpers_Http::getUploadedFile('content');

        $creatorPackageParser = new CreatorPackageParser($file['tmp_name']);
        $creatorPackageParser->validate();
        if($creatorPackageParser->isValid()){

            $result['valid'] = true;

            $manifest = $creatorPackageParser->getManifest();

            $result['typeIdentifier'] = $manifest['typeIdentifier'];
            $result['label'] = $manifest['label'];
            $interaction = $this->registry->get($manifest['typeIdentifier']);
            
            if(!is_null($interaction)){
                $result['exists'] = true;
            }
        }else{
            $result['package'] = $creatorPackageParser->getErrors();
        }

        $this->returnJson($result);
    }
    
    /**
     * Add a new custom interaction from the uploaded zip package
     */
    public function add(){

        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();

        try{
            $replace= true; //always set as "replaceable" and delegate decision to replace or not to the client side
            $file = tao_helpers_Http::getUploadedFile('content');
            $newInteraction = $this->registry->add($file['tmp_name'], $replace);

            $this->returnJson($this->filterInteractionData($newInteraction));
            
        }catch(FileUploadException $fe){

            $this->returnJson(array('error' => $fe->getMessage()));
        }
    }
    
    /**
     * Delete a custom interaction from the registry
     */
    public function delete(){

        $typeIdentifier = $this->getRequestParameter('typeIdentifier');
        $this->registry->remove($typeIdentifier);
        $ok = true;

        $this->returnJson(array(
            'success' => $ok
        ));
    }
    
    /**
     * Get the directory where the implementation sits
     * 
     * @param string $typeIdentifier
     * @return string
     */
    protected function getImplementationDirectory($typeIdentifier){
        $pci = $this->registry->get($typeIdentifier);
        if(is_null($pci)){
            $folder = $this->registry->getDevImplementationDirectory($typeIdentifier);
        }else{
            $folder = $pci['directory'];
        }
        return $folder;
    }

}