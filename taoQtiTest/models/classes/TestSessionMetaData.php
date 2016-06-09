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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoQtiTest\models;

use alroniks\dtms\DateInterval;
use alroniks\dtms\DateTime;
use DateTimeZone;
use qtism\common\datatypes\Duration;
use qtism\data\AssessmentItemRef;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\common\enums\Cardinality;
use Context;
use taoResultServer_models_classes_TraceVariable;
use qtism\runtime\tests\AssessmentTestSessionState;
use qtism\runtime\tests\RouteItem;

/**
 * Class manages test session metadata such as section or test exit codes and other.
 * 
 * Data will be stored as trace variable {@link \taoResultServer_models_classes_TraceVariable}.
 * 
 * Section level data stored as test variable {@link \taoResultServer_models_classes_ResultServerStateFull::storeTestVariable()}
 * prefixed with session identifier e.g. <i>SECTION_EXIT_CODE</i> will be stored as <i>SECTION_EXIT_CODE_assessmentSection-1</i>
 * 
 * 
 * Usage example:
 * <pre>
 * $sessionMetaData = new TestSessionMetaData($session);
 * $metaData = array(
 *   //Test level metadata
 *   'TEST' => array( 
 *      'TEST_EXIT_CODE' => TEST_CODE_COMPLETE,
 *   ),
 *   //Section level metadata
 *   'SECTION' => array(
 *      'SECTION_EXIT_CODE' => SECTION_CODE_COMPLETED_NORMALLY,
 *   ),
 *   //Item level metadata
 *   'ITEM' => array( //save item level metadata
 *      'ITEM_META_DATA' => 'value',
 *   ),
 * )
 * $sessionMetaData->save($metaData);
 * </pre>
 * 
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 *
 */
class TestSessionMetaData
{
    /**
     * Test session instance
     * @var AssessmentTestSession 
     */
    private $session;

    /**
     * Constructor.
     * @param \taoQtiTest_helpers_TestSession $session Test session instance.
     */
    public function __construct(\taoQtiTest_helpers_TestSession $session) {
       $this->session = $session;
    }
        
    /**
     * Save session metadata.
     * 
     * @param array $metaData Meta data array to be saved.
     * @param RouteItem $routeItem item for which data will be saved
     * @param string $assessmentSectionId section id for which data will be saved
     * Example:
     * array(
     *   'TEST' => array('TEST_EXIT_CODE' => 'IC'),
     *   'SECTION' => array('SECTION_EXIT_CODE' => 701),
     * )
     */
    public function save(array $metaData, RouteItem $routeItem = null, $assessmentSectionId = null)
    {
        $testUri = $this->session->getTest()->getUri();
        $resultServer = \taoResultServer_models_classes_ResultServerStateFull::singleton();

        foreach ($metaData as $type => $data) {
            foreach ($data as $key => $value) {
                $metaVariable = $this->getVariable($key, $value);

                if (strcasecmp($type, 'ITEM') === 0) {
                    if ($routeItem === null) {
                    $itemRef = $this->session->getCurrentAssessmentItemRef();
                    $occurence = $this->session->getCurrentAssessmentItemRefOccurence();
                    } else {
                        $itemRef = $routeItem->getAssessmentItemRef();
                        $occurence = $routeItem->getOccurence();
                    }

                    $itemUri = $this->getItemUri($itemRef);
                    $sessionId = $this->session->getSessionId();

                    $transmissionId = "${sessionId}.${itemRef}.${occurence}";
                    $resultServer->storeItemVariable($testUri, $itemUri, $metaVariable, $transmissionId);
                } elseif (strcasecmp($type, 'TEST') === 0) {
                    $resultServer->storeTestVariable($testUri, $metaVariable, $this->session->getSessionId());
                } elseif (strcasecmp($type, 'SECTION') === 0) {
                    //suffix section variables with _{SECTION_IDENTIFIER}
                    if ($assessmentSectionId === null) {
                    $assessmentSectionId = $this->session->getCurrentAssessmentSection()->getIdentifier();
                    }
                    $metaVariable->setIdentifier($key . '_' . $assessmentSectionId);
                    $resultServer->storeTestVariable($testUri, $metaVariable, $this->session->getSessionId());
                }
            }
        }
    }

    /**
     * Get current test session meta data array
     *
     * @return array test session meta data.
     */
    public function getData()
    {
        $request = Context::getInstance()->getRequest();
        $data = $request->hasParameter('metaData') ? $request->getParameter('metaData') : array();

        return $data;
    }

    /**
     * Get trace variable instance to save.
     *
     * @param string $identifier
     * @param string $value
     * @return taoResultServer_models_classes_TraceVariable variable instance to save.
     */
    private function getVariable($identifier, $value)
    {
        $metaVariable = new taoResultServer_models_classes_TraceVariable();
        $metaVariable->setIdentifier($identifier);
        $metaVariable->setBaseType('string');
        $metaVariable->setCardinality(Cardinality::getNameByConstant(Cardinality::SINGLE));
        $metaVariable->setTrace($value);

        return $metaVariable;
    }

    /**
     * Get test session instance
     * @return AssessmentTestSession|\taoQtiTest_helpers_TestSession
     */
    public function getTestSession()
    {
        return $this->session;
    }

    /**
     * Retrieve information about passed items
     * @return DateTime
     * @throws \common_exception_Error
     */
    public function getStartSectionTime()
    {
        $itemResults        = array();
        $assessmentItemsRef = $this->getTestSession()->getCurrentAssessmentSection()->getComponentsByClassName('assessmentItemRef');

        /** @var ExtendedAssessmentItemRef $itemRef */
        foreach ($assessmentItemsRef as $itemRef) {
            $itemResults[] = $this->getItemStartTime($itemRef);
        }
        $sectionStart = min(array_filter($itemResults));

        return $sectionStart;
    }

    /**
     * @param AssessmentItemRef $itemRef
     *
     * @return DateTime
     */
    public function getItemStartTime($itemRef)
    {
        $itemResults   = array();
        $itemStartTime = null;

        $ssid         = $this->getTestSession()->getSessionId();
        $resultServer = \taoResultServer_models_classes_ResultServerStateFull::singleton();
        $collection   = $resultServer->getVariables("{$ssid}.{$itemRef->getIdentifier()}.{$this->getTestSession()->getCurrentAssessmentItemRefOccurence()}");

        foreach ($collection as $vars) {
            foreach ($vars as $var) {
                if ($var->variable instanceof taoResultServer_models_classes_TraceVariable && $var->variable->getIdentifier() === 'ITEM_START_TIME_SERVER') {
                    $itemResults[] = $var->variable->getValue();
                }
            }
        }

        $itemResults = array_map(function ($ts) {
            $itemStart = (new DateTime('now', new DateTimeZone('UTC')));
            $itemStart->setTimestamp($ts);

            return $itemStart;
        }, $itemResults);

        if ( ! empty( $itemResults )) {
            $itemStartTime = min($itemResults);
        }

        return $itemStartTime;
    }

    /**
     * Get the URI referencing the Assessment Item (in the knowledge base)
     *
     * @param AssessmentItemRef $itemRef
     * @return string A URI.
     */
    private function getItemUri(AssessmentItemRef $itemRef)
    {
        $href = $itemRef->getHref();
        $parts = explode('|', $href);

        return $parts[0];
    }
}