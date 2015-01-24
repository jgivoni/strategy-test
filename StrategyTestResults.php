<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'PeriodTestResults.php';

/**
 * Holds the results of a strategy test
 */
class StrategyTestResults
{
    /**
     * Iterations simulated for the strategy
     * @var int
     */
    public $simulations;

    /**
     * Total conversions for the strategy
     * @var int
     */
    public $conversions;

    /**
     * Total x-sales for the strategy
     * @var int
     */
    public $xSales;

    /**
     * Total revenue for the strategy
     * @var float
     */
    public $revenue;

    /**
     * Adds the results for a single period to the strategy results
     * 
     * @param PeriodTestResults $results
     */
    public function addPeriodResults(PeriodTestResults $results)
    {
        $this->simulations++;
        $this->revenue += $results->revenue;
        $this->conversions += $results->conversions;
        $this->xSales += $results->xSales;
        return $this;
    }

    public function getAvgRevenue()
    {
        return (float) $this->revenue / (float) $this->simulations;
    }

}
