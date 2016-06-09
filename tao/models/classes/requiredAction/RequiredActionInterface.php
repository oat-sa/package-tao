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
 *
 */

namespace oat\tao\model\requiredAction;
use oat\oatbox\PhpSerializable;

/**
 * Interface RequiredActionInterface
 *
 * RequiredAction is action which should be executed by user before performing any activities in the TAO
 *
 * @package oat\tao\model\requiredAction
 * @author Aleh Hutnilau <hutnikau@1pt.com>
 */
interface RequiredActionInterface extends PhpSerializable
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param RequiredActionRuleInterface $rule
     * @return mixed
     */
    public function setRule(RequiredActionRuleInterface $rule);

    /**
     * @return RequiredActionRuleInterface[]
     */
    public function getRules();

    /**
     * Execute an action.
     * @return mixed
     */
    public function execute();

    /**
     * Mark action as completed.
     * @return mixed
     */
    public function completed();

    /**
     * Check whether the action must be executed.
     * @return boolean
     */
    public function mustBeExecuted();

}