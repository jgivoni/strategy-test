<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'AbstractTest.php';
require_once 'StrategyTestResults.php';
require_once 'PeriodTest.php';
require_once 'PeriodTestConditions.php';

/**
 * Test a strategy in a number of iterations
 */
class StrategyTest extends AbstractTest {
	/**
	 * Returns the results of the strategy test
	 * @param Strategy $strategy
	 * @param int $iterations
	 * @return StrategyTestResults
	 */
	public function getResults(Strategy $strategy, $iterations)
	{
		$conditions = new PeriodTestConditions();
		$conditions->experiences = $this->_conditions->experiences;
		$conditions->strategy = $strategy;
		$conditions->visistPerDay = $this->_conditions->visitsPerDay;
		$test = new PeriodTest($conditions);
		$revenue = 0;
		for ($iteration = 0; $iteration < $iterations; $iteration++) {
			$results = $test->getResults($this->_conditions->daysPerPeriod);
			$revenue += $results->revenue;
		}
		$results = new StrategyTestResults();
		$results->revenue = $revenue;
		return $results;
	}
}
