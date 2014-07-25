<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\processing;

use qtism\data\rules\TemplateRuleCollection;
use qtism\data\QtiComponent;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Template processing consists of one or more templateRules that are followed by 
 * the cloning engine or delivery system in order to assign values to the template 
 * variables. Template processing is identical in form to responseProcessing except 
 * that the purpose is to assign values to template variables, not outcome variables.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TemplateProcessing extends QtiComponent {
    
    /**
     * The collection of TemplateRule objects composing
     * the template processing.
     * 
     * @var TemplateRuleCollection
     * @qtism-bean-property
     */
    private $templateRules;
    
    /**
     * Create a new TemplateProcessing object.
     * 
     * @param TemplateRuleCollection $templateRules A collection of at least one TemplateRule object.
     * @throws InvalidArgumentException If $templateRules is an empty collection.
     */
    public function __construct(TemplateRuleCollection $templateRules) {
        $this->setTemplateRules($templateRules);
    }
    
    /**
     * Set the collection of TemplateRule objects composing
     * the template processing.
     * 
     * @param TemplateRuleCollection $templateRules A collection of at least one TemplateRule object.
     * @throws InvalidArgumentException If $templateRules is an empty collection.
     */
    public function setTemplateRules(TemplateRuleCollection $templateRules) {
        if (count($templateRules) > 0) {
            $this->templateRules = $templateRules;
        }
        else {
            $msg = "A 'templateProcessing' element must contain at lease one 'templateRule' element, none given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the collection of TemplatRule objects composing
     * the template processing.
     * 
     * @return TemplateRuleCollection A collection of at least one TemplateRule object.
     */
    public function getTemplateRules() {
        return $this->templateRules;
    }
    
    public function getComponents() {
        return $this->getTemplateRules();
    }
    
    public function getQtiClassName() {
        return 'templateProcessing';
    }
}