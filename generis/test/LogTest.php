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

class LogTest extends GenerisPhpUnitTestRunner {
	
	const RUNS = 1000;
    
    protected function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
	}
	
	public function testFileAppender()
	{
		$tfile = $this->createFile();
		$dfile = $this->createFile();
		$efile = $this->createFile();

		common_log_Dispatcher::singleton()->init(array(
			array(
				'class'			=> 'SingleFileAppender',
				'threshold'		=> common_Logger::TRACE_LEVEL,
				'file'			=> $tfile,
			),
			array(
				'class'			=> 'SingleFileAppender',
				'mask'			=> 2 , // 000010
				'file'			=> $dfile,
			),
			array(
				'class'			=> 'SingleFileAppender',
				'threshold'		=> common_Logger::ERROR_LEVEL,
				'file'			=> $efile,
			)
		));
		common_Logger::singleton()->enable();
		
		common_Logger::t('message');
		$this->assertEntriesInFile($tfile, 1);
		$this->assertEntriesInFile($dfile, 0);
		$this->assertEntriesInFile($efile, 0);
		
		common_Logger::d('message');
		$this->assertEntriesInFile($tfile, 2);
		$this->assertEntriesInFile($dfile, 1);
		$this->assertEntriesInFile($efile, 0);
		
		common_Logger::e('message');
		$this->assertEntriesInFile($tfile, 3);
		$this->assertEntriesInFile($dfile, 1);
		$this->assertEntriesInFile($efile, 1);
		
		common_Logger::singleton()->disable();
		
		common_Logger::d('message');
		$this->assertEntriesInFile($tfile, 3);
		$this->assertEntriesInFile($dfile, 1);
		$this->assertEntriesInFile($efile, 1);
		
		common_Logger::singleton()->restore();
		
		common_Logger::d('message');
		$this->assertEntriesInFile($tfile, 4);
		$this->assertEntriesInFile($dfile, 2);
		$this->assertEntriesInFile($efile, 1);
		
		common_Logger::singleton()->restore();
	}
	
	public function testLogTags()
	{
		$tfile = $this->createFile();
		$this->assertEntriesInFile($tfile, 0);
		
		common_log_Dispatcher::singleton()->init(array(
			array(
				'class'			=> 'SingleFileAppender',
				'threshold'		=> common_Logger::TRACE_LEVEL,
				'file'			=> $tfile,
				'tags'			=> 'CORRECTTAG'
			)
		));
		common_Logger::singleton()->enable();
		
		common_Logger::t('message');
		$this->assertEntriesInFile($tfile, 0);
		
		common_Logger::t('message', 'WRONGTAG');
		$this->assertEntriesInFile($tfile, 0);
		
		common_Logger::t('message', 'CORRECTTAG');
		$this->assertEntriesInFile($tfile, 1);
		
		common_Logger::t('message', array('WRONGTAG', 'CORRECTTAG'));
		$this->assertEntriesInFile($tfile, 2);
		
		common_Logger::t('message', array('WRONGTAG', 'WRONGTAG2'));
		$this->assertEntriesInFile($tfile, 2);
		
		common_Logger::singleton()->restore();
		
	}
	
	public function assertEntriesInFile($pFile, $pCount) {
		if (file_exists($pFile)) {
			$count = count(file($pFile));
		} else {
			$count = 0;
		}
		$this->assertEquals($count, $pCount, 'Expected count '.$pCount.', had '.$count.' in file '.$pFile);
	}

	
	public function analyseLogPerformance()
	{
		common_Logger::singleton()->enable();
		$start = microtime(true);
		for ($i = 0; $i < self::RUNS; $i++) {
			// nothing
		}
		$emptyTime = microtime(true) - $start;
		echo "Idle run: ".$emptyTime."<br />";
		
		$start = microtime(true);
		for ($i = 0; $i < self::RUNS; $i++) {
			common_Logger::t('a trace test message');
		}
		$traceTime = microtime(true) - $start;
		echo "Trace run: ".$traceTime."<br />";
		
		$start = microtime(true);
		for ($i = 0; $i < self::RUNS; $i++) {
			common_Logger::i('a info test message');
		}
		$infoTime = microtime(true) - $start;
		echo "Info run: ".$infoTime."<br />";
		
		common_Logger::singleton()->restore();
		
		common_Logger::singleton()->disable();
		$start = microtime(true);
		for ($i = 0; $i < self::RUNS; $i++) {
			common_Logger::i('a disabled test message');
		}
		$disabledTime = microtime(true) - $start;
		echo "Disabled run: ".$disabledTime."<br />";
		common_Logger::singleton()->restore();
		
		$start = microtime(true);
		sleep(1);
		$testwait = microtime(true) - $start;
		echo "Wait: ".$testwait."<br />";
	    echo "ok";
	}
    
}