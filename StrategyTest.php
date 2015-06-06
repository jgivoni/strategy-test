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
        $this->printHeader($strategy);

        $conditions = new PeriodTestConditions($this->_conditions->key);
        $conditions->experiences = $this->_conditions->experiences;
        $conditions->strategy = $strategy;
        $conditions->visitsPerDay = $this->_conditions->visitsPerDay;

        $results = new StrategyTestResults();
        $queue = new \Ophp\Subway\Queue;
        for ($iteration = 0; $iteration < $iterations; $iteration++) {
            $this->printLapHeader($iteration, $iterations);
            
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
        
        $this->printFooter($results);

        return $results;
    }
    
    protected function printHeader($strategy) {
        echo "Testing strategy: {$strategy->name} (" . $strategy->displayConfig() . ")\n";
    }
    
    protected function printLapHeader($iteration, $iterations) {
        echo "Starting test " . ($iteration + 1) . " of $iterations   \r";
    }
    
    /**
     * 
     * @param StrategyTestResults $results
     */
    protected function printFooter($results) {
        echo "\n";
        echo pl('Avg. total revenue', $results->getAvgRevenue(), $this->_conditions->getBaselineRevenue(), $this->_conditions->getOptimalRevenue());
        echo "Rev std dev: {$results->revenueStdDev}\n";
        echo "EPC: " . number_format($results->getAvgRevenue() / (float) $this->_conditions->visitsPerDay / (float) $this->_conditions->daysPerPeriod, 3) . "\n";
        echo "Baseline EPC: " . number_format($this->_conditions->getBaselineRevenue() / (float) $this->_conditions->visitsPerDay / (float) $this->_conditions->daysPerPeriod, 3) . "\n";
        echo pl('Avg. total conversions', $results->getAvgConversions(), $this->_conditions->getBaselineConversions(), $this->_conditions->getOptimalConversions());
        echo "Conversions variation: {$results->getConversionsVariation()}\n";
        $finalTheoreticalEpc = 0; // Calculate what the epc should be according to the exposure of experiences
        foreach ($results->winnerCount as $key => $count) {
            $visitCount = $results->experiencesVisitCount[$key];
            $exposureRate = (float) $visitCount / (float) $this->_conditions->visitsPerDay / (float) $this->_conditions->daysPerPeriod / (float) $results->simulations;
            $exposurePercent = number_format(100 * $exposureRate, 1);
            echo "Exp $key: $count wins, $visitCount exposures ($exposurePercent%)\n";
            $finalTheoreticalEpc += $exposureRate * $this->_conditions->experiences[$key]->rpc;
        }
        echo "Avg. days to find winner: " . number_format($results->getAvgDaysToWinner(), 1) . "\n";
        echo "Final theoretical epc: " . number_format($finalTheoreticalEpc, 3) . "\n";

    }

}
