<?php

require_once 'RAdapter.php';

/**
 * Holds the results of a strategy test
 */
class StrategiesTestResults {

    /**
     *
     * @var array Of StrategyTestResults
     */
    public $strategyResults = [];

    /**
     * Adds the results for a single strategy to the strategies results
     * 
     * @param StrategyTestResults $results
     */
    public function addStrategyResults(StrategyTestResults $results) {
        $this->strategyResults[] = $results;
        return $this;
    }

    public function getWinnerStrategy()
    {
        /* @var $winner StrategyTestResults */
        $winner = null;
        /* @var $results StrategyTestResults */
        foreach ($this->strategyResults as $results) {
            if (!isset($winner) || $results->getAvgRevenue() > $winner->getAvgRevenue()) {
                $winner = $results;
            }
        }
        return $winner;
    }
    
    public function getLoserStrategy()
    {
        /* @var $loser StrategyTestResults */
        $loser = null;
        /* @var $results StrategyTestResults */
        foreach ($this->strategyResults as $results) {
            if (!isset($loser) || $results->getAvgRevenue() < $loser->getAvgRevenue()) {
                $loser = $results;
            }
        }
        return $loser;
    }

    /**
     * Returns the zScore
     * @return float
     */
    public function getZScore() {
        $winner = $this->getWinnerStrategy();
        $loser = $this->getLoserStrategy();
        $m1 = $winner->getAvgRevenue();
        $m2 = $loser->getAvgRevenue();
        $s1 = $winner->revenueStdDev;
        $s2 = $loser->revenueStdDev;
        $n1 = (float) $winner->simulations;
        $n2 = (float) $loser->simulations;
        
        if ($s1 == 0 || $s2 == 0 || $n1 == 0 || $n2 == 0) {
            return 0;
        }
        $z = ($m1-$m2)/sqrt($s1*$s1/$n1+$s2*$s2/$n2);
        return $z;
    }

    /**
     * Returns the P value for the differece between the best and the worst strategy
     * @return float
     */
    public function getPValue() {
        $z = $this->getZScore();
        $r = new RAdapter();
        $p = $r->execute("pnorm($z)")[0][0]; // Two tailed
        return (1 - $p) * 2;
    }
}
