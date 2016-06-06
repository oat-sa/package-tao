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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Adapter for RDF/RDFS format
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_data_GenerisAdapterRdf
    extends tao_helpers_data_GenerisAdapter
{

    /**
     * Import a XML file as is into the ontology
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string source
     * @param  Class destination
     * @param  string namespace
     * @return boolean
     */
    public function import($source,  core_kernel_classes_Class $destination = null, $namespace = null)
    {
        $returnValue = (bool) false;

        
        
        $api = core_kernel_impl_ApiModelOO::singleton();
		$localModel = rtrim(common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri(), '#');
			
    	if(!is_null($destination) && file_exists($source)){
			
			$destModel = substr($destination->getUri(), 0, strpos($destination->getUri(), '#'));
			$returnValue = $api->importXmlRdf($destModel, $source);
		}
		else if (file_exists($source) && !is_null($namespace)){
			$returnValue = $api->importXmlRdf($namespace, $source);
		}
		else if (file_exists($source)){
			$returnValue = $api->importXmlRdf($localModel, $source);
		}
        
        

        return (bool) $returnValue;
    }

    /**
     * Export to xml-rdf the ontology of the Class in parameter.
     * All the ontologies are exported if the class is not set
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class source
     * @return string
     */
    public function export( core_kernel_classes_Class $source = null)
    {
        $rdf = '';
        
		if(is_null($source)){
		    return core_kernel_api_ModelExporter::exportAll();
		}

		$graph = new EasyRdf_Graph();
		if ($source->isClass()) {
            $this->addClass($graph, $source);
		} else {
		    $this->addResource($graph, $source);
		}
		$format = EasyRdf_Format::getFormat('rdfxml');
		return $graph->serialise($format);
    }
    
    /**
     * Add a class to the graph
     * 
     * @param EasyRdf_Graph $graph
     * @param core_kernel_classes_Class $resource
     * @ignore
     */
    private function addClass(EasyRdf_Graph $graph, core_kernel_classes_Class $resource) {
        $this->addResource($graph, $resource);
    	foreach($resource->getInstances(false) as $instance){
		    $this->addResource($graph, $instance);
		}
        foreach($resource->getSubClasses(false) as $subclass){
            $this->addClass($graph, $subclass);
        }
        foreach ($resource->getProperties(false) as $property) {
            $this->addResource($graph, $property);
        }
        
    }
    
    /**
     * Add a resource to the graph
     * 
     * @param EasyRdf_Graph $graph
     * @param core_kernel_classes_Resource $resource
     * @ignore
     */
    private function addResource(EasyRdf_Graph $graph, core_kernel_classes_Resource $resource) {
        foreach ($resource->getRdfTriples() as $triple) {
            if (!empty($triple->lg)) {
                $graph->addLiteral($triple->subject, $triple->predicate, $triple->object, $triple->lg);
            } elseif (common_Utils::isUri($triple->object)) {
                $graph->add($triple->subject, $triple->predicate, $triple->object);
            } else {
                $graph->addLiteral($triple->subject, $triple->predicate, $triple->object);
            }
        }
    }

}