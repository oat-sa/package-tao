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

use oat\generis\test\GenerisPhpUnitTestRunner;


class ExceptionTest extends GenerisPhpUnitTestRunner {
    
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
	}
    
    // Method used in the testInvalidArgumentTypeException
    private function wrongArgumentType($object)
    {
        // the function expects a common_Object as first argument
        if(!($object instanceof common_Object) ||
                ( ($object instanceof common_Object) && is_subclass_of($object, 'common_Object'))){
            throw new common_exception_InvalidArgumentType(__CLASS__, __METHOD__, 1, 'common_Object', $object);
        }
    }
    
    public function testInvalidArgumentTypeException()
    {
        try{
            $myResource = new core_kernel_classes_Resource('NIMP');
            $this->wrongArgumentType($myResource);
            $this->assertFalse(true);
        }catch(common_exception_InvalidArgumentType $exception){
            $this->assertTrue(true);
        }
    }
}
