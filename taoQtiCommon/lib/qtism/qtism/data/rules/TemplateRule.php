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

/**
 * From IMS QTI:
 * 
 * A template rule is either a templateCondition or a simple action. Template rules 
 * define the light-weight programming language necessary for creating cloned items.
 * Note that this programming language contains a minimal number of control structures, 
 * more complex cloning rules are outside the scope of this specification.
 * 
 * An expression used in a templateRule must not refer to the value of a response 
 * variable or outcome variable. It may only refer to the values of the template 
 * variables.
 * 
 * Template rules are follwed in the order given. Variables updated by a rule take 
 * their new value when evaluated as part of any following rules.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface TemplateRule extends Rule {
    
}