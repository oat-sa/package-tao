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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Profiler class
 *
 * @author Somsack Sipasseuth ,<sam@taotesting.com>
 * @package generis
 
 */
class common_Profiler
{
	/*
	 * @var common_Profiler
	 */
    private static $instance = null;
	private $enabled = false;
    /**
     * @var int|mixed
     */
    private $startTime = 0;
    /**
     * @var array
     */
    private $startTimeLogs = array();
	
    /**
     * @var array
     */
    private $elapsedTimeLogs = array();
	
    /**
     * @var array
     */
    private $slowQueries = array();
    private $slowestQueries = array();
	private $queries = array();
	
	
    /**
     * @var int
     */
    private $slowQueryTimer = 0;
	
    /**
     * @var int
     */

    /**
     * Singleton
     *
     * @static
     * @return null
     */
    public static function singleton()
    {
        $returnValue = null;

		if (is_null(self::$instance)){
			self::$instance = new self();
        }
		$returnValue = self::$instance;

        return $returnValue;
    }

    /**
     * Constructor
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     * @access private
     */
    private function __construct()
    {
		$this->startTime = self::getCurrentTime();
		$this->implementor = common_profiler_Dispatcher::singleton();
		$this->enabled = (bool)$this->implementor->hasAppender();
	}

    /**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     * @return mixed
     */
    protected function getCurrentTime()
    {
		return microtime(true);
	}

    /**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function register()
    {
		if($this->isEnabled()){
			register_shutdown_function(array($this, 'shutdownProfiler'));
		}
		
	}

    /**
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function shutdownProfiler()
    {
		
		if($this->isEnabled()){
			$this->logContext();
			$this->logEventTimer();
			$this->logMemoryPeakUsage();
			$this->logQueriesCount();
			$this->logQueriesSlowest();
			$this->logQueriesSlow();
			$this->logQueriesStat();
			$this->flush();
		}
		
	}

    /**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logEventTimer(){
		if($this->isEnabled('timer')){
			$total = $this->getCurrentTime() - $this->startTime;
			$sumDurations = 0;
			foreach ($this->elapsedTimeLogs as $event => $duration) {
					$this->implementor->logTimer($event, $duration, $total);
					if ($event == 'start' || $event == 'dispatch'){
						$sumDurations += $duration;
					}
			}
			$uncovered = $total - $sumDurations;
				$this->implementor->logTimer('???', $uncovered, $total);
		}
	}
	
	/**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logMemoryPeakUsage(){
		if($this->isEnabled('memoryPeak')){
			$memPeak = memory_get_peak_usage(true);
			$memMax = ini_get('memory_limit');
				if (substr($memMax, -1) == 'M') {
				$memMax = substr($memMax, 0, -1);
					$memMax = $memMax * 1048576;
			}

				$this->implementor->logMemoryPeak($memPeak, $memMax);
		}
	}
	
	/**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logQueriesCount(){
		$this->implementor->logQueriesCount(core_kernel_classes_DbWrapper::singleton()->getNrOfQueries());
	}
	
	/**
     *
     */
    protected function logContext(){
		if($this->isEnabled('context')){
			$context = new common_profiler_Context();
			$this->implementor->logContext($context);
		}
	}
	
	/**
     * @static
     * @param $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function start($flag){
		self::singleton()->startTimer($flag);
	}
	
	/**
     * @static
     * @param $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function stop($flag){
		self::singleton()->stopTimer($flag);
	}
	
	/**
     * @static
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     *
     */
    public static function queryStart(){
		self::singleton()->startSlowQuery();
	}
	
	/**
     * @static
     * @param $statement
     * @param array $params
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function queryStop($statement, $params = array()){
		self::singleton()->stopSlowQuery($statement, $params);
	}
	
	/**
     * @param string $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function startTimer($flag = 'global'){
		if($this->isEnabled()) {
		    $this->startTimeLogs[$flag] = $this->getCurrentTime();
		}
	}
	
	/**
	 * 
     * @param string $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function stopTimer($flag = 'global'){
		if($this->isEnabled()){
			$startTime = isset($this->startTimeLogs[$flag]) ? $this->startTimeLogs[$flag] : $this->startTime;
			$this->elapsedTimeLogs[$flag] = $this->getCurrentTime() - $startTime;
		}
	}
	
	/**
	 * 
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function startSlowQuery(){
		if($this->isEnabled()) {
		    $this->slowQueryTimer = $this->getCurrentTime();
		}
	}
	
	public function isEnabled($appenderName = ''){
		$returnValue = ($this->enabled && $this->implementor->hasAppender());
		if(!empty($appenderName)){
			$returnValue &= $this->implementor->isEnabled($appenderName);
		}
		return $returnValue;
	}
	
	/**
	 * 
     * @param $statement
     * @param array $params
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function stopSlowQuery($statement, $params = array()){
		
		if($this->isEnabled() && $this->slowQueryTimer){//log only if timer has been started
			
			$time = $this->getCurrentTime() - $this->slowQueryTimer;
			$query = new common_profiler_Query($statement, $params, $time);
			$statementKey = $query->getStatementKey();
			
			if($this->implementor->isEnabled('slowQueries')){
				$threshold = $this->implementor->getConfigOption('slowQueries', 'threshold');
				if (!is_null($threshold)) {
					if (1000 * $time > $threshold) {//compare to threshold (ms)
						if (!isset($this->slowQueries[$statementKey])) {
							$this->slowQueries[$statementKey] = array();
						}
						$this->slowQueries[$statementKey][] = $query;
					}
				}
			}
			
			if($this->implementor->isEnabled('slowestQueries')){
				$count = $this->implementor->getConfigOption('slowestQueries', 'count');
				if (!is_null($count) && $count > 0) {
					$this->slowestQueries[] = $query;
					uasort($this->slowestQueries, function($q1, $q2) {
						if ($q1->getTime() == $q2->getTime()) {
							return 0;
						}
						return ($q1->getTime() > $q2->getTime()) ? -1 : 1;
					});
					
					if (count($this->slowestQueries) > $count) {
						array_pop($this->slowestQueries);
					}
				}
			}
			
			if($this->implementor->isEnabled('queries')){
				if (!isset($this->queries[$statementKey])) {
					$this->queries[$statementKey] = array('statement' => $statement, 'count' => 0, 'cumul' => 0);
				}
			$this->queries[$statementKey]['count']++;
			$this->queries[$statementKey]['cumul'] += $time;
		}
		
		}
		
		$this->slowQueryTimer = 0;
	}
	
    protected function logQueriesSlow(){
		if($this->isEnabled('slowQueries')){
			$this->implementor->logQueriesSlow($this->slowQueries);
			}
		}
	
	protected function logQueriesSlowest(){
		if($this->isEnabled('slowestQueries')){
			$this->implementor->logQueriesSlowest($this->slowestQueries);
	}
	}
	
	protected function logQueriesStat(){
		if($this->isEnabled('queries')){
			$this->implementor->logQueriesStat($this->queries);
		}
	}
	
	/**
     * Flush log data
	 * 
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function flush(){
		$this->implementor->flush();
	}
	
	/**
	 * (experimental)
	 * call common_Profiler::singleton()->initMysqlProfiler(); when after the DbWrapper has been constructed
	 * 
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function initMysqlProfiler(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		if($dbWrapper instanceof core_kernel_classes_MysqlDbWrapper){
			$dbWrapper->exec('SET profiling = 1');
			$this->log('MySQL profiler enabled');
		}
	}

	/*
	 * (experimental)
	 * log profiles from mysql built-in profiler
	 * 
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logMysqlProfiles(){
		
		$this->initMysqlProfiler();
		
		//get sql profile data
		$profiles = array();
		$profile = array();
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		$sqlProfilesResult = $dbWrapper->query('SHOW PROFILES');
		while ($row = $sqlProfilesResult->fetch()) {
			$id = $row['Query_ID'];

			$sqlDataResult = $dbWrapper->query('SHOW PROFILE FOR QUERY ' . $id);
			$profileData = array();
			while ($r = $sqlDataResult->fetch()) {
				$profileData[$r[0]] = $r;
			}

			$profiles[$id] = $profileData;
			$profiles[$id]['duration'] = $row['Duration'];
			$profiles[$id]['query'] = $row['Query'];
		}

		$sqlResult = $dbWrapper->query('SHOW PROFILE');
		while ($row = $sqlResult->fetch()) {
			$profile[$row[0]] = $row;
		}

		$report = array(
			'queriesCount' => $dbWrapper->getNrOfQueries(),
			'profile' => $profile,
			'profiles' => $profiles
		);
		
		$this->log(json_encode($report));
	}
	
}