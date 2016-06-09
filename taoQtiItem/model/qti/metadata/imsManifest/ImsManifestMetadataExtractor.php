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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model\qti\metadata\imsManifest;

use \DOMDocument;
use \DOMXPath;
use \DOMText;
use oat\taoQtiItem\model\qti\metadata\MetadataExtractionException;
use oat\taoQtiItem\model\qti\metadata\MetadataExtractor;

/**
 * A MetadataExtractor implementation
 * This implementation simply iterate through nodes and create an array of MetadataSimpleInstance object
 *
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 */
class ImsManifestMetadataExtractor implements MetadataExtractor 
{

    /**
     * @see MetadataExtractor::extract()
     */
    public function extract($manifest)
    {
        if ($manifest instanceof DOMDocument) {
            
            $bases = array();
            
            // get the base for paths.
            $xpath = new DOMXPath($manifest);
            foreach ($xpath->query('namespace::*', $manifest->ownerDocument) as $node) {
                $bases[str_replace('xmlns:', '', $node->nodeName)] = $node->nodeValue;
            }
            
            $manifestElt = $manifest->documentElement;
            $rootNs = $manifestElt->namespaceURI;
            
            // Extract metadata on a <resource basis>.
            $xpath->registerNamespace('man', $rootNs);
            
            // Prepare data structure to be returned.
            $metadata = array();
            
            $resourcesElt = $xpath->query('/man:manifest/man:resources/man:resource');
            foreach ($resourcesElt as $resourceElt) {
                $identifier = $resourceElt->getAttribute('identifier');
                $href = $resourceElt->getAttribute('href');
                $type = $resourceElt->getAttribute('type');
                
                $metadataElts = $xpath->query('man:metadata', $resourceElt);
                foreach ($metadataElts as $metadataElt) {
                    // Ask for metadata domains.
                    $domainElts = $xpath->query('*[not(self::man:schema) and not(self::man:schemaversion)]', $metadataElt);
                    foreach ($domainElts as $domainElt) {
                        
                        $trail = array();
                        $visited = array();
                        $path = array();
                        $parent = null;
                        
                        array_push($trail, $domainElt);
                        
                        while (count($trail) > 0) {
                            
                            $currentElt = array_pop($trail);
                            
                            if (!$currentElt instanceof DOMText && in_array($currentElt, $visited, true) === false) {
                                // Hierarchical node, 1st visit.
                                
                                // Push current for a future ascending exploration.
                                array_push($trail, $currentElt);
                                
                                // Push children on the trail for descending exploration.
                                $nodesToExplore = $currentElt->childNodes;
                                
                                for ($i = ($nodesToExplore->length - 1); $i >= 0; $i--) {
                                    array_push($trail, $nodesToExplore->item($i));
                                }
                                
                                // Set current as visited.
                                array_push($visited, $currentElt);
                                
                                // Update the path.
                                array_push($path, $currentElt->namespaceURI . '#' . $currentElt->localName);
                                
                                // Reference parent for leaf nodes.
                                $parent = $currentElt;
                            } elseif ($currentElt instanceof DOMText && ctype_space($currentElt->wholeText) === false) {
                                
                                // Leaf node, 1st and only visit.
                                $metadataValue = new ImsManifestMetadataValue($identifier, $type, $href, $path, $currentElt->wholeText);
                                if ($parent !== null && $parent->hasAttributeNS($bases['xml'], 'lang')) {
                                    $metadataValue->setLanguage($parent->getAttributeNS($bases['xml'], 'lang'));
                                }
                                
                                if (isset($metadata[$identifier]) === false) {
                                    $metadata[$identifier] = array();
                                }
                                
                                $metadata[$identifier][] = $metadataValue;
                            } else if (in_array($currentElt, $visited, true) === true) {
                                // Hierarchical node, second visit (ascending).
                                
                                // Update the path.
                                array_pop($path);
                            }
                        }
                    }
                }
            }
            
            return $metadata;
            
        } else {
            throw new MetadataExtractionException(__('The manifest argument must be an instance of DOMDocument.'));
        }
    }
}