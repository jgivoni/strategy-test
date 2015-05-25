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

require_once 'Ophp/subway/SubwayQueue.php';

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
    public function getResults(Strategy $strategy, $iterations) {
        echo "Testing strategy: {$strategy->name} (" . $strategy->displayConfig() . ")\n";

        $conditions = new PeriodTestConditions($this->_conditions->key);
        $conditions->experiences = $this->_conditions->experiences;
        $conditions->strategy = $strategy;
        $conditions->visitsPerDay = $this->_conditions->visitsPerDay;

        $results = new StrategyTestResults();
        $queue = new \Ophp\Subway\Queue;
        for ($iteration = 0; $iteration < $iterations; $iteration++) {
            echo "Starting test " . ($iteration + 1) . " of $iterations   \r";
            $test = new PeriodTest($conditions);
            $process = function() use ($results, $test) {
                $periodResults = $test->getResults($this->_conditions->daysPerPeriod);
                if (isset($periodResults)) {
                    $results->addPeriodResults($periodResults);
                    return true;
                } else {
                    return false;
                }
            };
            $queue->addProcess($process);
        }
        $queue->execute();

        $results->collapse();
        echo "\n";
        echo pl('Avg. total revenue', $results->getAvgRevenue(), $this->_conditions->getBaselineRevenue(), $this->_conditions->getOptimalRevenue());
        echo "Rev std dev: {$results->revenueStdDev}\n";
        echo "EPC: " . number_format($results->getAvgRevenue() / (float) $this->_conditions->visitsPerDay / (float) $this->_conditions->daysPerPeriod, 2) . "\n";
        echo "Baseline EPC: " . number_format($this->_conditions->getBaselineRevenue() / (float) $this->_conditions->visitsPerDay / (float) $this->_conditions->daysPerPeriod, 2) . "\n";
        echo pl('Avg. total conversions', $results->getAvgConversions(), $this->_conditions->getBaselineConversions(), $this->_conditions->getOptimalConversions());
        echo "Conversions variation: {$results->getConversionsVariation()}\n";
        foreach ($results->winnerCount as $key => $count) {
            echo "Exp $key: $count wins\n";
        }
        echo "Avg. days to find winner: " . number_format($results->getAvgDaysToWinner(), 1) . "\n";

        return $results;
    }

}
