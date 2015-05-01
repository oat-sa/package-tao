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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * 
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package generis
 
 */
class common_profiler_LoggerAppender
        extends common_profiler_Appender
{

    /**
     * Short description of method init
     *
     * @access public
     * @author Sam
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration){
		
        $returnValue = (bool) false;
    	
		parent::init($configuration);
		
    	$this->tag = (isset($configuration['tag']) && !empty($configuration['tag'])) ? strval($configuration['tag']) : 'PROFILER';
		
    	$returnValue = true;

        return (bool) $returnValue;
    }
	
	protected function log($msg){
		common_Logger::d($msg, array($this->tag));
	}
	
	public function logContext(common_profiler_Context $context){
		$this->log('Profiling action called: '.$context->getCalledUrl());
	}
	
	public function logTimer($flag, $duration, $total){
		$totalRounded = round($total * 1000, 3);
		$this->log($flag . ': ' . round($duration * 1000, 3) . 'ms/'.$totalRounded.'ms (' . common_profiler_PrettyPrint::percentage($duration, $total) . '%)');
	}
	
	public function logMemoryPeak($memPeak, $memMax){
		$memPc = common_profiler_PrettyPrint::percentage($memPeak, $memMax);
		$this->log('peak mem usage: '.common_profiler_PrettyPrint::memory($memPeak).'/'.common_profiler_PrettyPrint::memory($memMax).' ('.$memPc.'%)');
	}
	
	public function logQueriesCount($count){
		$this->log($count.' queries for this request');
	}
	
	public function logQueriesSlow($slowQueries){
		foreach($slowQueries as $queries){
			$count = count($queries);
			for($i=0; $i<$count; $i++){
				$this->log('slow query: '.$queries[$i]->getTime('ms').'ms : '.$queries[$i]->getStatement());
			}
		}
	}
	
	public function logQueriesSlowest($slowestQueries){
		$i = 1;
		foreach($slowestQueries as $query){
			$this->log('slowest query#'.$i.': '.$query->getTime('ms').'ms : '.$query->getStatement());
			$i++;
		}
	}
	
	public function logQueriesStat($queries){
		$count = $this->getConfigOption('queries', 'count');
		if(!is_null($count)){
			foreach ($queries as $query) {
				if ($query['count'] > $count){
					$this->log('Query count: ' . $query['statement'] . ' => ' . $query['count']);
				}
			}
		}
	}
	
	public function flush(){
		//nothing to flush here
	}
	
} /* end of abstract class common_profiler_LoggerAppender */

?>