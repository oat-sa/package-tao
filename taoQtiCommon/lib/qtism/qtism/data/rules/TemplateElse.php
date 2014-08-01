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

namespace qtism\data\rules;

use qtism\data\QtiComponent;

/**
 * The QTI templateElse class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TemplateElse extends QtiComponent {
    
    /**
     * The collection of TemplateRule objects to be evaluated.
     * 
     * @var TemplateRuleCollection
     */
    private $templateRules;
    
    /**
     * Create a new TemplateElse object.
     * 
     * @param TemplateRuleCollection $templateRules A collection of TemplateRule objects.
     */
    public function __construct(TemplateRuleCollection $templateRules) {
        $this->setTemplateRules($templateRules);
    }
    
    /**
     * Set the TemplateRule objects to be evaluated.
     * 
     * @param TemplateRuleCollection $templateRules A collection of TemplateRule objects.
     */
    public function setTemplateRules(TemplateRuleCollection $templateRules) {
        $this->templateRules = $templateRules;
    }
    
    /**
     * Get the TemplateRule objects to be evaluated.
     * 
     * @return TemplateRuleCollection A collection of TemplateRule objects.
     */
    public function getTemplateRules() {
        return $this->templateRules;
    }
    
    public function getComponents() {
        return new $this->getTemplateRules();
    }
    
    public function getQtiClassName() {
        return 'templateElse';
    }
}