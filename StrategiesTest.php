<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'AbstractTest.php';
require_once 'StrategyTest.php';
require_once 'StrategyTestConditions.php';

/**
 * Test a set of strategies against each other
 */
class StrategiesTest extends AbstractTest
{
    /**
     * Returns the results of the strategies test
     * @param Strategy $strategy
     * @param int $iterations
     * @return StrategyTestResults
     */
    public function getResults($strategies)
    {
        $conditions = new StrategyTestConditions($this->_conditions->key);
        $conditions->experiences = $this->_conditions->experiences;
        $conditions->visitsPerDay = $this->_conditions->visitsPerDay;
        $conditions->daysPerPeriod = $this->_conditions->daysPerPeriod;
        $test = new StrategyTest($conditions);
		$time = time();
		echo date('Y-m-d H:i:s') . "\n";
        foreach ($strategies as $strategy) {
            $test->getResults($strategy, $this->_conditions->iterationsPerStrategy);
            echo "-" . date('i\m-s\s', time() - $time) . "-\n";
        }
    }

}
