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
     * Standard deviation for the revenue
     * @var float 
     */
    public $revStdDev;
    
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
        if ($this->visits > 0) {
            // Calculate the combined std dev
            // This is the correct method:
            // http://stats.stackexchange.com/questions/55999/is-it-possible-to-find-the-combined-standard-deviation
            // But I'm using a simpler one which should be ok when the samples are taken from the same population
            $this->revStdDev = sqrt((pow($this->revStdDev,2)*$this->visits + pow($results->revStdDev,2)*$results->visits)/($this->visits + $results->visits));
        } else {
            $this->revStdDev = $results->revStdDev;
        }
        $this->visits += $results->visits;
        $this->revenue += $results->revenue;
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
