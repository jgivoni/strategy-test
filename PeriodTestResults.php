<?php

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
    public $visits = 0;

    /**
     * Total conversions for the period so far
     * @var int
     */
    public $conversions = 0;

    /**
     * Total x-sales for the period so far
     * @var int
     */
    public $xSales = 0;

    /**
     * Total revenue for the period so far
     * @var float
     */
    public $revenue = 0;
    
    /**
     * Total sum of squared revenue per visit
     * Used to calculate variance/standard deviation later
     */
    public $sumSqRev = 0;
            
    /**
     * Key of the winning experience by the end of the period
     * @var string
     */
    public $winner;
    
    /**
     * How many days did it take before the final winner had final majority?
     * @var int
     */
    public $daysToWinner;

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
        $this->sumSqRev += $results->sumSqRev;
        $this->conversions += $results->conversions;
        $this->xSales += $results->xSales;
        foreach ($results->experiencesResults as $key => $subresults) {
            $this->experiencesResults[$key]->addDayResults($subresults);
        }
        return $this;
    }

    /**
     * Cleans up the results by summarizing the subresults
     * and then removing them to save space
     */
    public function collapse()
    {
       // @todo 
        return $this;
    }

}
