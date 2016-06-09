<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
use oat\tao\test\TaoPhpUnitTestRunner;
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * @author Jerome Bogaerts, <taosupport@tudor.lu>
 * @package tao
 
 */
class UriHelperTestCase extends TaoPhpUnitTestRunner {
    
    public function setUp()
    {		
        parent::setUp();
		TaoPhpUnitTestRunner::initTest();
	}
	
	public function testUriDomain(){
		$uri = 'http://www.google.fr';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEquals($domain, 'www.google.fr');
		
		$uri = 'http://www.google.fr/';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEquals($domain, 'www.google.fr');
		
		$uri = 'http://www.google.fr/translate';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEquals($domain, 'www.google.fr');
		
		$uri = 'http://www.google.fr/translate?word=yes';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEquals($domain, 'www.google.fr');
		
		$uri = 'ftp://sub.domain.filetransfer.ulc.ag.be';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEquals($domain, 'sub.domain.filetransfer.ulc.ag.be');
		
		$uri = 'flupsTu8tridou:kek';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertNull($domain, "domain should be null but is equal to '${domain}'.");
		
		$uri = 'http://mytaoplatform/';
		$domain = tao_helpers_Uri::getDomain($uri);
		$this->assertEquals($domain, 'mytaoplatform');
	}
	
	public function testUriPath(){
		$uri = 'http://www.google.fr';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertNull($path);
		
		$uri = 'http://www.google.fr/';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEquals($path, '/');
		
		$uri = 'http://www.google.fr/translate';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEquals($path, '/translate');
		
		$uri = 'http://www.google.fr/translate?word=yes';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEquals($path, '/translate');
		
		$uri = 'http://www.google.fr/translate/funky?word=yes';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEquals($path, '/translate/funky');
		
		$uri = 'http://www.google.fr/translate/funky/?word=yes';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEquals($path, '/translate/funky/');
		
		$uri = 'ftp://sub.domain.filetransfer.ulc.ag.be';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertNull($path);
		
		$uri = 'flupsTu8tridoujkek';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertNull($path);
		
		$uri = 'http://mytaoplatform/';
		$path = tao_helpers_Uri::getPath($uri);
		$this->assertEquals($path, '/');
	}
	
	public function testIsValidAsCookieDomain(){
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('http://mytaoplatform'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('http://my-tao-platform'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('mytaoplatform'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('mytaoplatform/items/'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain(''));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://mytaoplatform.com'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my-tao-platform.ru'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://www.mytaoplatform.com'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://www.-my-tao-platform.ru'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my.taoplatform.com'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my.tao.platform.qc.ca'));
		$this->assertTrue(tao_helpers_Uri::isValidAsCookieDomain('http://my.TAO.plAtfOrm.qc.cA'));
		$this->assertFalse(tao_helpers_Uri::isValidAsCookieDomain('http://.my.tao.platform.qc.ca'));		
	}
}
?>