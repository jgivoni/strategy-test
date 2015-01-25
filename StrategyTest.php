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
class StrategyTest extends AbstractTest
{
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
        $conditions->visitsPerDay = $this->_conditions->visitsPerDay;
        $test = new PeriodTest($conditions);
        $results = new StrategyTestResults();
        for ($iteration = 0; $iteration < $iterations; $iteration++) {
            echo "Running test " . ($iteration+1) . " of $iterations   \r";
            $results->addPeriodResults($test->getResults($this->_conditions->daysPerPeriod));
        }
        return $results;
    }

}
