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
     * Standard deviation for the revenue
     * @var float
     */
    public $revStdDev;

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
        if ($this->visits > 0) {
            // Calculate the new std dev incrementally
            // @see: http://math.stackexchange.com/questions/102978/incremental-computation-of-standard-deviation
            // sd1=sqrt((n-2)/(n-1)*sd0^2+1/n*(rev-epc0)^2)
        
            $mean = $this->getRpc(); // Previous mean
            $n = (float) $this->visits + 1; // Current observation count (min 2)
            $sd = $this->revStdDev; // Previous standard deviation
            $x = (float) $results->revenue; // Current observation
                
            $this->revStdDev = sqrt(($n-2)/($n-1)*pow($sd,2) + pow($x-$mean,2)/$n);
        } else {
            $this->revStdDev = 0;
        }
        $this->visits++;
        $this->revenue += $results->revenue;
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
