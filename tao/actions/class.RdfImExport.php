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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 
 *
 */
class tao_actions_RdfImExport extends tao_actions_CommonModule {
    
    public function index() {
        $import = new tao_actions_form_NamespaceImportForm();
        $form = $import->getForm();
        if ($form->isSubmited()) {
            if ($form->isValid()) {
                $this->import($form);
            }
        }
        $this->setData('importForm', $form->render());
        
        $export = new tao_actions_form_NamespaceExportForm();
        $form = $export->getForm();
        if ($form->isSubmited()) {
            if ($form->isValid()) {
                $this->export($form);
            }
        }
        $this->setData('exportForm', $form->render());
        
        $this->setView('settings/rdfImExport.tpl', 'tao');
    }
    
    private function import($form) {
        common_Logger::i('import submited');
        $fileInfo = $form->getValue('source');
        $file = $fileInfo['uploaded_file'];
        	
        //validate the file to import
        $parser = new tao_models_classes_Parser($file, array('extension' => 'rdf'));
        	
        $parser->validate();
        if(!$parser->isValid()){
            $errors = array();
            foreach ($parser->getErrors() as $error) {
                $errors[] = $error->_toString();
            }
            $this->setData('importErrors', $errors);
        } else{
            //initialize the adapter
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if($adapter->import($file)){
                $this->setData('message', __('Data imported successfully'));
            } else{
                common_Logger::w('Import failed for '.$file);
                $this->setData('importErrors', array(__('Nothing imported')));
            }
        }
        
    }

    private function export(tao_helpers_form_Form $form) {
        
        common_Logger::i('export submited');
        
        //file where we export
        $name = $form->getValue('filename').'.rdf';
        $path = tao_helpers_Export::getExportFile();

        if(!tao_helpers_File::securityCheck($path, true)){
            throw new Exception('Unauthorized file name');
        }

        $modelIds = array();
        foreach($form->getValue('rdftpl') as $key => $value){
            if(preg_match("/^ns_/", $key)){
                $modelIds[] = (int)str_replace('ns_', '', $key);
            }
        };
        $rdf = core_kernel_api_ModelExporter::exportModels($modelIds);

        //save it
        if(!empty($rdf)){
            common_Logger::i('Saving to '.$path);
            if(file_put_contents($path, $rdf)){
                $relPath = tao_helpers_Export::getRelativPath($path);
                $this->setData('download', _url('downloadExportedFiles', 'Export', null, array('filePath' => $relPath, 'fileName' => $name)));
            }
        } else {
            common_Logger::w('Exported RDF was empty');
        }
    }
    
}