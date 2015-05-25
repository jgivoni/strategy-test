<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'AbstractTest.php';
require_once 'StrategyTest.php';
require_once 'StrategyTestConditions.php';
require_once 'StrategiesTestResults.php';

/**
 * Test a set of strategies against each other
 */
class StrategiesTest extends AbstractTest {

    /**
     * Timestamp at start
     * @var int
     */
    protected $time;
    
    protected $results;
    
    /**
     * Returns the results of the strategies test
     * @param Strategy $strategy
     * @param int $iterations
     * @return StrategyTestResults
     */
    public function getResults($strategies) {
        $this->time = time();
        $this->printHeader();
        
        $conditions = new StrategyTestConditions($this->_conditions->key);
        $conditions->experiences = $this->_conditions->experiences;
        $conditions->visitsPerDay = $this->_conditions->visitsPerDay;
        $conditions->daysPerPeriod = $this->_conditions->daysPerPeriod;
        $test = new StrategyTest($conditions);
        $this->results = new StrategiesTestResults;
        
        foreach ($strategies as $strategy) {
            $strategyResults = $test->getResults($strategy, $this->_conditions->iterationsPerStrategy);
            $this->results->addStrategyResults($strategyResults);
            
            $this->printLapFooter();
        }
        
        $this->printFooter();
    }

    protected function printHeader() {
        echo date('Y-m-d H:i:s') . "\n";
    }
    
    protected function printLapFooter() {
        echo "-" . date('i\m-s\s', time() - $this->time) . "-\n";
    }
    
    protected function printFooter() {
        echo "Z-score: " . $this->results->getZScore() . "\n";
        echo "P-value: " . (1 - $this->results->getPValue()) . "\n";
        echo "Confidence: " . $this->results->getPValue()*100 . "%\n";
    }
}
