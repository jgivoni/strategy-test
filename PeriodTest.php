<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'PeriodTestResults.php';
require_once 'DayTest.php';
require_once 'DayTestConditions.php';

/**
 * Tests the strategy over a period given in days
 */
class PeriodTest extends AbstractTest
{
    /**
     * Returns the results of the period test
     * @param int $days
     * @return PeriodTestResults
     */
    public function getResults($days)
    {
        $conditions = new PeriodTestConditions($this->_conditions->key);
        $conditions->experiences = $this->_conditions->experiences;
        $test = new DayTest($conditions);
        $results = new PeriodTestResults($conditions->experiences);
        $weightingRules = $this->getInitialWeightingRules($conditions->experiences);
        for ($day = 0; $day < $days; $day++) {
            echo "\t\t\tRunning day " . ($day+1) . " of $days ({$this->_conditions->visitsPerDay} visits per day)   \r";
            $results->addDayResults($test->getResults($weightingRules, $this->_conditions->visitsPerDay));
            $weightingRules = $this->getAdjustedWeightingRules($results, $day);
            $winner = $this->_getWinnerKey($weightingRules);
            if ($winner != $results->winner) {
                $results->winner = $winner;
                $results->daysToWinner = $day;
            }
            
            // Logging
            $this->csv($weightingRules);
            $this->log(sprintf("Day %d:\t\t%6dv\t%6dc\t%6dr\n",
                $day, $results->visits, $results->conversions, $results->revenue));
            foreach ($results->experiencesResults as $key => $e) {
                $this->log(sprintf("Experience $key:\t%6dv\t%6dc\t%6dr\tNew weight: %d \n",
                    $e->visits, $e->conversions, $e->revenue, $weightingRules[$key]));
            }
            $this->log("-\n");
        }
        
        return $results;
    }

    /**
     * Returns even weight on each experience
     * @param array $experiences
     */
    protected function getInitialWeightingRules($experiences)
    {
        $weightingRules = [];
        foreach ($experiences as $key => $experience) {
            $weightingRules[$key] = 1;
        }
        return $weightingRules;
    }

    /**
     * Returns the new weighting rules according to the results and
     * the strategy we are testing
     * 
     * @param PeriodTestResults $results
     */
    protected function getAdjustedWeightingRules($results, $day)
    {
        $visitsPerExperience = [];
        $conversionsPerExperience = [];
        $xSalesPerExperience = [];
        $revenuePerExperience = [];
        foreach ($results->experiencesResults as $exRes) {
            $visitsPerExperience[] = $exRes->visits;
            $conversionsPerExperience[] = $exRes->conversions;
            $xSalesPerExperience[] = $exRes->xSales;
            $revenuePerExperience[] = $exRes->revenue;
        }
        $weights = $this->_conditions->strategy->getWeights($visitsPerExperience, $conversionsPerExperience, $xSalesPerExperience, $revenuePerExperience);
        return array_combine(array_keys($results->experiencesResults), $weights);
    }

    /**
     * Returns the experience key for the experience with the heights weight
     * 
     * @param array $weightingRules
     * @return string
     */
    protected function _getWinnerKey($weightingRules)
    {
        $bestWeight = 0;
        foreach ($weightingRules as $key => $weight) {
            if ($weight > $bestWeight) {
                $bestWeight = $weight;
                $winnerKey = $key;
            } elseif ($weight == $bestWeight) {
                if ((float) mt_rand() / (float) mt_getrandmax() > 0.5) {
                    $winnerKey = $key;
                }
            }
        }
        return $winnerKey;
    }

}
