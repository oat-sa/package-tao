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
 * Description of InlineModalFeedbackItemUpdate
 *
 * @author sam
 */
class ItemUpdateInlineFeedback extends ItemUpdater
{

    /**
     * Update the item content by wrapping the modalfeedback body into a new wrapper
     *
     * @param oat\taoQtiItem\modal\Item $item
     * @param string $itemFile
     * @return boolean
     */
    protected function updateItem(\oat\taoQtiItem\model\qti\Item $item, $itemFile)
    {
        $changed = false;
        $responses = $item->getResponses();

        foreach ($responses as $response) {

            $responseIdentifier = $response->attr('identifier');
            $rules              = $response->getFeedbackRules();

            foreach ($rules as $rule) {
                $modalFeedbacks = array();
                if ($rule->getFeedbackThen()) {
                    $modalFeedbacks[] = $rule->getFeedbackThen();
                }
                if ($rule->getFeedbackElse()) {
                    $modalFeedbacks[] = $rule->getFeedbackElse();
                }
                foreach ($modalFeedbacks as $modalFeedback) {
                    $feedbackXml = simplexml_load_string($modalFeedback->toQti());
                    if ($feedbackXml->div[0] && $feedbackXml->div[0]['class'] && preg_match('/^x-tao-wrapper/',
                            $feedbackXml->div[0]['class'])) {
                        //the item body has not already been wrapped by the new wrapper <div class="x-tao-wrapper w-tao-relatedOutcome-{{response.identifier}}">
                        continue;
                    }
                    $message = $modalFeedback->getBody()->getBody();
                    $modalFeedback->getBody()->edit('<div class="x-tao-wrapper x-tao-relatedOutcome-'.$responseIdentifier.'">'.$message.'</div>',
                        true);
                    $changed = true;
                }
            }
        }

        return $changed;
    }
}