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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 *
 */

namespace oat\taoQtiItem\model\update;

/**
 * Description of ItemFixGhostResponse
 *
 * @author sam
 */
class ItemFixGhostResponse extends ItemUpdater
{

    private $templates = [
        'http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct',
        'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response',
        'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response_point'
    ];
    
    /**
     * Remove unused response declaration from the items and rp template misuse
     *
     * @param oat\taoQtiItem\modal\Item $item
     * @param string $itemFile
     * @return boolean
     */
    protected function updateItem(\oat\taoQtiItem\model\qti\Item $item, $itemFile)
    {
        $changed = false;
        $responses = $item->getResponses();
        $interactions = $item->getInteractions();
        $usedResponses = [];
        foreach($interactions as $interaction){
            $usedResponses[] = $interaction->attr('responseIdentifier');
        }
        foreach ($responses as $response) {
            $responseIdentifier = $response->attr('identifier');
            if(!in_array($responseIdentifier, $usedResponses)){
                $changed = true;
                $item->removeResponse($response);
            }
        }

        $xml        = simplexml_load_file($itemFile);
        $rpTemplate = (string) $xml->responseProcessing['template'];
        
        //detect wrong usage for standard standard response declaration
        $rp = $item->getResponseProcessing();
        if ($rp instanceof \oat\taoQtiItem\model\qti\response\TemplatesDriven && $rpTemplate) {
            if (count($interactions) > 1) {
                $changed = true;
            } else {
                $interaction = reset($interactions);
                if ($interaction && $interaction->attr('responseIdentifier') != 'RESPONSE') {
                    $changed = true;
                }
            }
        }

        return $changed;
    }
}