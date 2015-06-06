<?php

require_once 'RAdapter.php';
require_once 'PeriodTestResults.php';

/**
 * Holds the results of a strategy test
 */
class StrategyTestResults {

    /**
     *
     * @var array Of PeriodTestResults
     */
    public $periodResults = [];

    /**
     * Array of count of visits for each experience - how many times was it shown?
     * @var array of int
     */
    public $experiencesVisitCount = [];
    
    /**
     * Iterations simulated for the strategy
     * @var int
     */
    public $simulations = 0;

    /**
     * Total conversions for the strategy
     * @var int
     */
    public $conversions = 0;

    /**
     * Total x-sales for the strategy
     * @var int
     */
    public $xSales = 0;

    /**
     * Total revenue for the strategy
     * @var float
     */
    public $revenue = 0;

    /**
     *
     * @var float
     */
    public $revenueStdDev;

    /**
     *
     * @var float
     */
    public $conversionsStdDev;

    /**
     * Key/value pairs for each experience and the number of times it won the period
     * @var array
     */
    public $winnerCount = [];

    /**
     * Total days to find a winner
     * (Divide by iterations to find average)
     * @var int
     */
    public $daysToWinner;

    /**
     * Adds the results for a single period to the strategy results
     * 
     * @param PeriodTestResults $results
     */
    public function addPeriodResults(PeriodTestResults $results) {
        /* @var $e PeriodTestResults */
        if (empty($this->winnerCount)) {
            foreach ($results->experiencesResults as $key => $e) {
                $this->winnerCount[$key] = 0;
            }
        }
        foreach ($results->experiencesResults as $key => $e) {
            if (!isset($this->experiencesVisitCount[$key])) {
                $this->experiencesVisitCount[$key] = 0;
            }
            $this->experiencesVisitCount[$key] += $e->visits;
        }

        $this->daysToWinner += $results->daysToWinner;
        $this->simulations++;
        $this->revenue += $results->revenue;
        $this->conversions += $results->conversions;
        $this->xSales += $results->xSales;
        $this->periodResults[] = $results->collapse();
        if ($results->winner) {
            $this->winnerCount[$results->winner] ++;
        }
        return $this;
    }

    /**
     * Returns the average revenue
     * @return float
     */
    public function getAvgRevenue() {
        return (float) $this->revenue / (float) $this->simulations;
    }

    public function getAvgConversions() {
        return (float) $this->conversions / (float) $this->simulations;
    }

    public function getAvgXSales() {
        return (float) $this->xSales / (float) $this->simulations;
    }

    public function getAvgConversionsInclXSales() {
        return ((float) $this->conversions + (float) $this->xSales) / (float) $this->simulations;
    }

    public function getAvgDaysToWinner() {
        return (float) $this->daysToWinner / (float) $this->simulations;
    }

    /**
     * Summarizes the results
     */
    public function collapse() {
        // Calculate standard deviation of revenue
        $revenueList = implode(',', array_map(function($results) {
                    /* @var $results PeriodTestResults */
                    return $results->revenue;
                }, $this->periodResults));
        $r = new RAdapter();
        $this->revenueStdDev = $r->execute("values=c($revenueList);\n sd(values)")[0][0];

        // Calculate standard deviation of conversions
        $conversionsList = implode(',', array_map(function($results) {
                    /* @var $results PeriodTestResults */
                    return $results->conversions;
                }, $this->periodResults));
        $r = new RAdapter();
        $this->conversionsStdDev = $r->execute("values=c($conversionsList);\n sd(values)")[0][0];

        return $this;
    }

    public function getConversionsVariation() {
        $variation = $this->conversionsStdDev * 3 / $this->getAvgConversions();
        return "+/-" . number_format($variation * 100, 1) . "%";
    }

}
