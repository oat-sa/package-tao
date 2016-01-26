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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\qtiItemPci\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\qtiItemPci\model\CreatorPackageParser;

class CreatorPackageParserTest extends TaoPhpUnitTestRunner
{

    protected $qtiService;

    /**
     * tests initialization
     * load qti service
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
    }

    public function testValidate(){

        $packageValid = dirname(__FILE__).'/samples/valid.zip';
        $parser = new CreatorPackageParser($packageValid);
        $parser->validate();
        $this->assertTrue($parser->isValid());
        
        $packageValid = dirname(__FILE__).'/samples/likertValid.zip';
        $parser = new CreatorPackageParser($packageValid);
        $parser->validate();
        $this->assertTrue($parser->isValid());
        
        $packageInvalidExt = dirname(__FILE__).'/samples/invalidExt.bz2';
        $parser = new CreatorPackageParser($packageInvalidExt);
        $parser->validate();
        $this->assertFalse($parser->isValid());
        
        $packageInvalidManifest = dirname(__FILE__).'/samples/invalidManifest.zip';
        $parser = new CreatorPackageParser($packageInvalidManifest);
        $parser->validate();
        $this->assertFalse($parser->isValid());
        
        $packageMissingManifest = dirname(__FILE__).'/samples/missingManifest.zip';
        $parser = new CreatorPackageParser($packageMissingManifest);
        $parser->validate();
        $this->assertFalse($parser->isValid());
        
        $packageMissingManifest = dirname(__FILE__).'/samples/missingHook.zip';
        $parser = new CreatorPackageParser($packageMissingManifest);
        $parser->validate();
        $this->assertFalse($parser->isValid());
        
        $packageMissingManifest = dirname(__FILE__).'/samples/invalidManifestEntries.zip';
        $parser = new CreatorPackageParser($packageMissingManifest);
        $parser->validate();
        $this->assertFalse($parser->isValid());
    }
    
    public function testGetManifest(){
        
        $packageValid = dirname(__FILE__).'/samples/valid.zip';
        $parser = new CreatorPackageParser($packageValid);
        $manifest = $parser->getManifest();
        $this->assertTrue(!!count($manifest));
        $this->assertEquals($manifest['typeIdentifier'], 'likertScaleInteraction');
        
    }

}