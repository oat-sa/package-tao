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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\tao\model\media\sourceStrategy\HttpSource;
use oat\tao\helpers\Template;

/**
 * This class tests the state of the ontology
 *
 * @author Joel Bout, <taosupport@tudor.lu>
 * @package tao
 
 */
class HttpSourceTest extends TaoPhpUnitTestRunner {

	public function testGetBaseName()
	{
	    $mediaSource = new HttpSource();
	    $this->assertEquals('tao.png', $mediaSource->getBaseName(Template::img('tao.png', 'tao')));
	    $this->assertEquals('tao.png', $mediaSource->getBaseName(Template::img('tao.png?a=b', 'tao')));
	    
	    $this->setExpectedException(tao_models_classes_FileNotFoundException::class);
	    $mediaSource->getBaseName('http://notevenavaliddomain');
	}
}