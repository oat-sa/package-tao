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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);     
 * 
 */

namespace oat\taoQtiItem\controller;

use oat\taoQtiItem\helpers\QtiRunner;
use qtism\runtime\tests\AssessmentItemSession;
use \taoItems_actions_ItemRunner;

/**
 * Abstract QTI Item Runner Controller
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @author Joel Bout <joel@taotesting.com>
 * @author Somsack Sipasseuth <sam@taotesting.com>
 * @package taoQTI

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
abstract class AbstractQtiItemRunner extends taoItems_actions_ItemRunner {

    protected $variableContents = null;

    protected function setInitialVariableElements() {

        //get initial content variable elements to be displayed: rubricBlocks, feedbacks, variable math, template elements, template variable
        $this->setData('contentVariableElements', $this->getRubricBlocks());
    }

    protected function getContentVariableElements() {

        $dir = $this->getDirectory($this->getRequestParameter('itemDataPath'));
        $this->variableContents = QtiRunner::getContentVariableElements($dir);

        return $this->variableContents;
    }

    protected function getPrivateFolder() {
        $dir = $this->getDirectory($this->getRequestParameter('itemDataPath'));
        return QtiRunner::getPrivateFolderPath($dir);
    }

    protected function getRubricBlocks() {
        //@todo : pass the right view from item/service api?
        $view = 'candidate';
        $dir = $this->getDirectory($this->getRequestParameter('itemDataPath'));
        return QtiRunner::getRubricBlocks($dir, $view);
    }

    protected function getTemplateElements() {
        
        //process templateRules
        //return the template values
    }

    protected function getFeedbacks(AssessmentItemSession $itemSession) {
        $dir = $this->getDirectory($this->getRequestParameter('itemDataPath'));
        return QtiRunner::getFeedbacks($dir, $itemSession);
    }
}