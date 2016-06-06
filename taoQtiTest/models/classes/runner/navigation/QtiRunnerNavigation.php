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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoQtiTest\models\runner\navigation;

use oat\taoQtiTest\models\event\QtiMoveEvent;
use oat\taoQtiTest\models\runner\RunnerServiceContext;
use oat\taoQtiTest\models\runner\QtiRunnerServiceContext;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\runtime\tests\AssessmentItemSession;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\event\EventManager;

/**
 * Class QtiRunnerNavigation
 * @package oat\taoQtiTest\models\runner\navigation
 */
class QtiRunnerNavigation
{
    /**
     * Gets a QTI runner navigator
     * @param string $direction
     * @param string $scope
     * @return RunnerNavigation
     * @throws \common_exception_InvalidArgumentType
     * @throws \common_exception_NotImplemented
     */
    public static function getNavigator($direction, $scope)
    {
        $className = __NAMESPACE__ . '\QtiRunnerNavigation' . ucfirst($direction) . ucfirst($scope);
        if (class_exists($className)) {
            $navigator = new $className();
            if ($navigator instanceof RunnerNavigation) {
                return $navigator;
            } else {
                throw new \common_exception_InvalidArgumentType('Navigator must be an instance of RunnerNavigation');
            }
        } else {
            throw new \common_exception_NotImplemented('The action is invalid!');
        }
    }

    /**
     * @param string $direction
     * @param string $scope
     * @param RunnerServiceContext $context
     * @param integer $ref
     * @throws \common_exception_InvalidArgumentType
     * @throws \common_exception_NotImplemented
     * @return boolean
     */
    public static function move($direction, $scope, RunnerServiceContext $context, $ref)
    {
        $navigator = self::getNavigator($direction, $scope);

        if ($context instanceof QtiRunnerServiceContext) {
            $from = $context->getTestSession()->isRunning() === true ? $context->getTestSession()->getRoute()->current() : null;
            $event = new QtiMoveEvent(QtiMoveEvent::CONTEXT_BEFORE, $context->getTestSession(), $from);
            ServiceManager::getServiceManager()->get(EventManager::CONFIG_ID)->trigger($event);
        }

        $result = $navigator->move($context, $ref);

        if ($context instanceof QtiRunnerServiceContext) {
            $to = $context->getTestSession()->isRunning() === true ? $context->getTestSession()->getRoute()->current() : null;
            $event = new QtiMoveEvent(QtiMoveEvent::CONTEXT_AFTER, $context->getTestSession(), $from, $to);
            ServiceManager::getServiceManager()->get(EventManager::CONFIG_ID)->trigger($event);
        }

        return $result;
    }

    /**
     * Check if a timed section is exited
     * @param RunnerServiceContext $context
     * @param int $nextPosition
     */
    public static function checkTimedSectionExit(RunnerServiceContext $context, $nextPosition)
    {
        /* @var AssessmentTestSession $session */
        $session = $context->getTestSession();
        $route = $session->getRoute();
        $section = $session->getCurrentAssessmentSection();
        $limits = $section->getTimeLimits();

        $isJumpOutOfSection = false;
        if (($nextPosition >= 0) && ($nextPosition < $route->count())) {
            $nextSection = $route->getRouteItemAt($nextPosition);

            $isJumpOutOfSection = ($section->getIdentifier() !== $nextSection->getAssessmentSection()->getIdentifier());
        }

        if ($isJumpOutOfSection && $limits != null && $limits->hasMaxTime()) {
            $components = $section->getComponents();

            foreach ($components as $object) {
                if ($object instanceof ExtendedAssessmentItemRef) {
                    $items = $session->getAssessmentItemSessions($object->getIdentifier());

                    foreach ($items as $item) {
                        if ($item instanceof AssessmentItemSession) {
                            $item->endItemSession();
                        }
                    }
                }
            }
        }
    }
}
