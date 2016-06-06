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

use \Exception;

/**
 * Class RequiredAction
 *
 * RequiredAction is action which should be executed by user before performing any activities in the TAO
 *
 * @package oat\tao\model\requiredAction\implementation
 * @author Aleh Hutnilau <hutnikau@1pt.com>
 */
abstract class RequiredActionAbstract implements RequiredActionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var RequiredActionRuleInterface[]
     */
    protected $rules = [];

    /**
     * RequiredAction constructor.
     * @param string $name
     * @param RequiredActionRuleInterface[] $rules
     * @throws Exception
     */
    public function __construct($name, array $rules = [])
    {
        $this->name = $name;
        foreach ($rules as $rule) {
            $this->setRule($rule);
        }
    }

    /**
     * Execute an action
     * @param array $params
     * @return mixed
     */
    abstract public function execute(array $params = []);

    /**
     * Whether the action must be executed.
     * @return boolean
     */
    public function mustBeExecuted()
    {
        $result = $this->checkRules();

        return $result;
    }

    /**
     * Mark action as completed.
     * @return mixed
     */
    public function completed()
    {
        $rules = $this->getRules();
        foreach ($rules as $rule) {
            $rule->completed();
        }
    }

    /**
     * Get array of rules
     * @return RequiredActionRuleInterface[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Add rule to rules list
     * @param RequiredActionRuleInterface $rule
     * @return void
     */
    public function setRule(RequiredActionRuleInterface $rule)
    {
        $rule->setRequiredAction($this);
        $this->rules[] = $rule;
    }

    /**
     * Get action name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     */
    public function __toPhpCode()
    {
        $class = get_class($this);
        $name = $this->name;
        $rules = \common_Utils::toHumanReadablePhpString($this->getRules());
        return "new $class(
            '$name',
            $rules
        )";
    }

    /**
     * Check rules whether action must be performed.
     * If at least one rule returns true the action will be performed.
     * If result is `true` then action must be performed.
     * @return bool
     */
    protected function checkRules()
    {
        $rules = $this->getRules();
        $result = false;

        foreach ($rules as $rule) {
            if ($rule->check()) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}