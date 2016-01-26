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
 * importhandler for RDF
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class tao_models_classes_import_RdfImporter implements tao_models_classes_import_ImportHandler
{

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getLabel()
     */
    public function getLabel() {
    	return __('RDF');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getForm()
     */
    public function getForm() {
    	$form = new tao_models_classes_import_RdfImportForm();
    	return $form->getForm();
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::import()
     */
    public function import($class, $form) {
    	
        $fileInfo = $form->getValue('source');
		$file = $fileInfo['uploaded_file'];
			
		//validate the file to import
		$parser = new tao_models_classes_Parser($file, array('extension' => 'rdf'));
			
		$parser->validate();
		if(!$parser->isValid()){
			$report = common_report_Report::createFailure(__('Nothing imported'));
			$report->add($parser->getReport());
			return $report;
		} else{
		    return $this->flatImport($file, $class);
		}
    }
    
    /**
     * Imports the rdf file into the selected class
     * 
     * @param string $file
     * @param core_kernel_classes_Class $class
     * @return common_report_Report
     */
    private function flatImport($file, core_kernel_classes_Class $class) {
        $report = common_report_Report::createSuccess(__('Data imported successfully'));
        
        $graph = new EasyRdf_Graph();
        $graph->parseFile($file);

        // keep type property
        $map = array(
            RDF_PROPERTY => RDF_PROPERTY
        );
        
        foreach ($graph->resources() as $resource) {
            $map[$resource->getUri()] = common_Utils::getNewUri();
        }
        
        $format = EasyRdf_Format::getFormat('php');
        $data = $graph->serialise($format);
        
        foreach ($data as $subjectUri => $propertiesValues){
            $resource = new core_kernel_classes_Resource($map[$subjectUri]);
            $subreport = $this->importProperties($resource, $propertiesValues, $map, $class);
            $report->add($subreport);
        }
        return $report;
    }
    
    /**
     * Import the properties of the resource
     * 
     * @param core_kernel_classes_Resource $resource
     * @param array $propertiesValues
     * @param array $map
     * @param core_kernel_classes_Class $class
     * @return common_report_Report
     */
    private function importProperties(core_kernel_classes_Resource $resource, $propertiesValues, $map, $class) {
        if (isset($propertiesValues[RDF_TYPE])) {
            // assuming single Type
            if (count($propertiesValues[RDF_TYPE]) > 1) {
                return new common_report_Report(common_report_Report::TYPE_ERROR, __('Resource not imported due to multiple types'));
            } else {
                foreach ($propertiesValues[RDF_TYPE] as $k => $v) {
                    $classType = isset($map[$v['value']])
                    ? new core_kernel_classes_Class($map[$v['value']])
                    : $class;
                    //$resource->setType($classType);
                    $classType->createInstance(null, null, $resource->getUri());
                }
            }
            unset($propertiesValues[RDF_TYPE]);
        }
        
        if (isset($propertiesValues[RDFS_SUBCLASSOF])) {
            $resource = new core_kernel_classes_Class($resource);
            // assuming single subclass
            if (isset($propertiesValues[RDF_TYPE]) && count($propertiesValues[RDF_TYPE]) > 1) {
                return new common_report_Report(common_report_Report::TYPE_ERROR, __('Resource not imported due to multiple super classes'));
            }
            foreach ($propertiesValues[RDFS_SUBCLASSOF] as $k => $v) {
                $classSup = isset($map[$v['value']])
                ? new core_kernel_classes_Class($map[$v['value']])
                : $class;
                $resource->setSubClassOf($classSup);
            }
        
            unset($propertiesValues[RDFS_SUBCLASSOF]);
        }
        foreach ($propertiesValues as $prop=>$values){
            $property = new core_kernel_classes_Property(isset($map[$prop]) ? $map[$prop] : $prop);
            foreach ($values as $k => $v) {
                $value = isset($map[$v['value']]) ? $map[$v['value']] : $v['value'];
                if (isset($v['lang'])) {
                    $resource->setPropertyValueByLg($property, $value, $v['lang']);
                } else {
                    $resource->setPropertyValue($property, $value);
                }
            }
        }
        $msg = $resource instanceof core_kernel_classes_Class
            ? __('Successfully imported class "%s"', $resource->getLabel())
            : __('Successfully imported "%s"', $resource->getLabel());
        return new common_report_Report(common_report_Report::TYPE_SUCCESS, $msg);
    }

}
