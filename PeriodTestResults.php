<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Holds the results of a period test
 */
class PeriodTestResults
{
    /**
     * Subresults for the individual experiences
     * @var array of PeriodTestResults
     */
    public $experiencesResults = [];

    /**
     * Total visits simulated for the period so far
     * @var int
     */
    public $visits;

    /**
     * Total conversions for the day
     * @var int
     */
    public $conversions;

    /**
     * Total x-sales for the day
     * @var int
     */
    public $xSales;

    /**
     * Total revenue for the day
     * @var float
     */
    public $revenue;

    /**
     * Initialises a new period test results
     * 
     * Prepares the array of subresults per experience
     * 
     * @param array $experiences
     */
    public function __construct($experiences = [])
    {
        foreach ($experiences as $key => $experience) {
            $this->experiencesResults[$key] = new PeriodTestResults();
        }
    }

    /**
     * Adds the results for a single day to the period visits
     * 
     * @param DayTestResults $results
     */
    public function addDayResults(DayTestResults $results)
    {
        $this->visits += $results->visits;
        $this->revenue += $results->revenue;
        $this->conversions += $results->conversions;
        $this->xSales += $results->xSales;
        foreach ($results->experiencesResults as $key => $subresults) {
            $this->experiencesResults[$key]->addDayResults($subresults);
        }
        return $this;
    }

}
