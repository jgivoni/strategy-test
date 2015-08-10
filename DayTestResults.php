<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DayTestResults
{
    /**
     * Subresults for the individual experiences
     * @var array of DayTestResults
     */
    public $experiencesResults = [];

    /**
     * Total visits simulated for the day
     * @var int
     */
    public $visits = 0;

    /**
     * Total conversions for the day
     * @var int
     */
    public $conversions = 0;

    /**
     * Total x-sales for the day
     * @var int
     */
    public $xSales = 0;

    /**
     * Total revenue for the day
     * @var float
     */
    public $revenue = 0;
    
    /**
     * Total sum of squared revenue per visit
     * Used to calculate variance/standard deviation later
     */
    public $sumSqRev = 0;
            
    /**
     * Initialises a new daily test results
     * 
     * Prepares the array of subresults per experience
     * 
     * @param array $experiences
     */
    public function __construct($experiences = [])
    {
        foreach ($experiences as $key => $experience) {
            $this->experiencesResults[$key] = new DayTestResults();
        }
    }

    /**
     * Adds the results for a single visit to the daily visits
     * 
     * @param VisitTestResults $results
     */
    public function addVisitResults(VisitTestResults $results)
    {
        $this->visits++;
        $this->revenue += $results->revenue;
        $this->sumSqRev += pow($results->revenue, 2);
        $this->conversions += $results->conversion ? 1 : 0;
        $this->xSales += $results->xSales;
        
        if (isset($this->experiencesResults[$results->experienceKey])) {
            $this->experiencesResults[$results->experienceKey]->addVisitResults($results);
        }
        return $this;
    }

    public function getRpc()
    {
        return (float) $this->revenue / (float) $this->visits;
    }
    
    public function getCr()
    {
        return (float) $this->conversions / (float) $this->visits;
    }

}
