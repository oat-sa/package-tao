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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoQtiTest\models;

use oat\oatbox\service\ConfigurableService;
use qtism\runtime\tests\AssessmentTestSession;
use oat\taoDelivery\model\execution\DeliveryExecution;

/**
 * The SessionStateService
 *
 * Service used for pausing and resuming the delivery execution.
 * All timers in paused session will be paused.
 *
 * Usage example:
 * <pre>
 * //Pause session:
 * $sessionStateService = ServiceManager::getServiceManager()->get('taoQtiTest/SessionStateService');
 * $sessionStateService->pauseSession($session);
 *
 * //resume session:
 * $sessionStateService = ServiceManager::getServiceManager()->get('taoQtiTest/SessionStateService');
 * $sessionStateService->resumeSession($session);
 * </pre>
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 */
class SessionStateService extends ConfigurableService
{
    const SERVICE_ID = 'taoQtiTest/SessionStateService';
    
    const OPTION_STATE_FORMAT = 'stateFormat';
    
    /**
     * @var \taoDelivery_models_classes_execution_ServiceProxy
     */
    private $deliveryExecutionService;

    public function __construct(array $options = array())
    {
        $this->deliveryExecutionService = \taoDelivery_models_classes_execution_ServiceProxy::singleton();
        parent::__construct($options);
    }

    /**
     * Pause delivery execution.
     * @param AssessmentTestSession $session
     * @return boolean success
     */
    public function pauseSession(AssessmentTestSession $session) {
        $session->updateDuration();
        return $this->getDeliveryExecution($session)->setState(DeliveryExecution::STATE_PAUSED);
    }

    /**
     * Resume delivery execution
     * @param AssessmentTestSession $session
     */
    public function resumeSession(AssessmentTestSession $session) {
        $deliveryExecutionState = $this->getSessionState($session);
        if ($deliveryExecutionState === DeliveryExecution::STATE_PAUSED) {
            $this->updateTimeReference($session);
            $this->getDeliveryExecution($session)->setState(DeliveryExecution::STATE_ACTIVE);
        }
    }

    /**
     * Get delivery execution state
     * @param AssessmentTestSession $session
     * @return string
     */
    public function getSessionState(AssessmentTestSession $session) {
        $deliveryExecution = $this->getDeliveryExecution($session);
        return $deliveryExecution->getState()->getUri();
    }

    /**
     * Set time reference of current assessment item session to <i>now</i> instead of time of last update.
     * This ensures that time when delivery execution was paused will not be taken in account.
     * Make sure that method invoked right after retrieving assessment test session
     * and before the first AssessmentTestSession::updateDuration method call
     * @param AssessmentTestSession $session
     * @param \DateTime|null $time Time to be specified. Current time by default. Make sure that $time has UTC timezone.
     */
    public function updateTimeReference(AssessmentTestSession $session, \DateTime $time = null) {
        if ($time === null) {
            $time = new \DateTime('now', new \DateTimeZone('UTC'));
        }

        $itemSession = $session->getCurrentAssessmentItemSession();

        if ($itemSession) {
            $itemSession->setTimeReference($time);
            $session->updateDuration();
        }
    }

    /**
     * @param AssessmentTestSession $session
     * @return \taoDelivery_models_classes_execution_DeliveryExecution
     */
    private function getDeliveryExecution(AssessmentTestSession $session) {
        return $this->deliveryExecutionService->getDeliveryExecution($session->getSessionId());
    }

    /**
     * Returns appropriate JS service implementation for testRunner
     *
     * @param boolean $resetTimerAfterResume
     *
     * @return string
     */
    public function getClientImplementation($resetTimerAfterResume = false){
        if ($resetTimerAfterResume) {
            return 'taoQtiTest/testRunner/resumingStrategy/keepAfterResume';
        }
        return 'taoQtiTest/testRunner/resumingStrategy/resetAfterResume';
    }
    
    /**
     * Return a human readable description of the test session
     *  
     * @return string
     */
    public function getSessionDescription(\taoQtiTest_helpers_TestSession $session)
    {
        if ($session->isRunning()) {
            $config = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->getConfig('testRunner');
            $progressScope = isset($config['progress-indicator-scope']) ? $config['progress-indicator-scope'] : 'test';
            $progress = $this->getSessionProgress($session);
            $itemPosition = $progress[$progressScope];
            $itemCount = $progress[$progressScope . 'Length'];

            $format = $this->hasOption(self::OPTION_STATE_FORMAT)
                ? $this->getOption(self::OPTION_STATE_FORMAT)
                : __('%s - item %p/%c');
            $map = array(
                '%s' => $session->getCurrentAssessmentSection()->getTitle(),
                '%p' => $itemPosition,
                '%c' => $itemCount
            );
            return strtr($format, $map);
        } else {
            return __('finished');
        }
    }

    /**
     * Gets the current progress inside a delivery execution
     * @param \taoQtiTest_helpers_TestSession $session
     * @return array|bool
     */
    protected function getSessionProgress(\taoQtiTest_helpers_TestSession $session)
    {
        if ($session->isRunning() !== false) {
            $route = $session->getRoute();
            $routeItems = $route->getAllRouteItems();
            $offset = $route->getRouteItemPosition($routeItems[0]);
            $offsetPart = 0;
            $offsetSection = 0;
            $lastPart = null;
            $lastSection = null;

            $positions = [];
            $lengthParts = [];
            $lengthSections = [];
            $sectionIndex = 0;
            $partIndex = 0;

            // compute positions from the test map
            foreach ($routeItems as $routeItem) {
                $testPart = $routeItem->getTestPart();
                $partId = $testPart->getIdentifier();
                if ($lastPart != $partId) {
                    $offsetPart = 0;
                    $lastPart = $partId;
                    $partIndex ++;
                }

                $sections = $routeItem->getAssessmentSections();
                $sectionId = key(current($sections));
                if ($lastSection != $sectionId) {
                    $offsetSection = 0;
                    $lastSection = $sectionId;
                    $sectionIndex ++;
                }

                $offset ++;
                $offsetPart ++;
                $offsetSection ++;

                $lengthParts[$partIndex] = $offsetPart;
                $lengthSections[$sectionIndex] = $offsetSection;

                $positions[] = [
                    'test' => $offset,
                    'part' => $offsetPart,
                    'partId' => $partIndex,
                    'section' => $offsetSection,
                    'sectionId' => $sectionIndex,
                ];
            }

            $progress = $positions[$route->getPosition()];
            return [
                'test' => $progress['test'],
                'testPart' => $progress['part'],
                'testSection' => $progress['section'],
                'testLength' => $session->getRouteCount(),
                'testPartLength' => $lengthParts[$progress['partId']],
                'testSectionLength' => $lengthSections[$progress['sectionId']],
            ];
        }
        return false;
    }
}
