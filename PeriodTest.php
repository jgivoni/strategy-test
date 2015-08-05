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
class PeriodTest extends AbstractTest {

    protected $day = 0;
    protected $test;
    protected $periodResults;
    protected $weightingRules;
    protected $pendingAdjustedWeightingRules = false;

    /**
     * Returns the results of the period test
     * @param int $days
     * @return PeriodTestResults
     */
    public function getResults($days) {
        if ($this->day < $days) {
            if (!$this->pendingAdjustedWeightingRules) {
                if ($this->day == 0) {
                    $conditions = new PeriodTestConditions($this->_conditions->key);
                    $conditions->experiences = $this->_conditions->experiences;
                    $this->test = new DayTest($conditions);
                    $results = new PeriodTestResults($conditions->experiences);
                    $this->periodResults = $results;
                    $this->results = $results;
                    $weightingRules = $this->getInitialWeightingRules($conditions->experiences);
                    $this->weightingRules = $weightingRules;
                } else {
                    $results = $this->periodResults;
                    $weightingRules = $this->weightingRules;
                }

                echo "\t\t\t\tRunning day " . ($this->day + 1) . " of $days ({$this->_conditions->visitsPerDay} visits per day)   \r";
                $results->addDayResults($this->test->getResults($weightingRules, $this->_conditions->visitsPerDay));
                $this->pendingAdjustedWeightingRules = true;
            } else {
                $results = $this->periodResults;
                $weightingRules = $this->weightingRules;
            }
            $weightingRules = $this->getAdjustedWeightingRules($results);
            if (isset($weightingRules)) {
                $this->weightingRules = $weightingRules;
                $winner = $this->_getWinnerKey($weightingRules);
                if (isset($winner) && $winner != $results->winner) {
                    $results->winner = $winner;
                    $results->daysToWinner = $this->day;
                } else {
                    $results->winner = null;
                    $results->daysToWinner = null;
                }

                // Logging
                $this->csv($weightingRules);
                $this->log(sprintf("Day %d:\t\t%6dv\t%6dc\t%6dr\n", $this->day, $results->visits, $results->conversions, $results->revenue));
                foreach ($results->experiencesResults as $key => $e) {
                    $this->log(sprintf("Experience $key:\t%6dv\t%6dc\t%6dr\t%6sepc New weight: %d \n", $e->visits, $e->conversions, $e->revenue, 
                            number_format((float)$e->revenue / (float)$e->visits, 2),
                            $weightingRules[$key]));
                }
                $this->log("-\n");

                $this->day++;
                $this->pendingAdjustedWeightingRules = false;
            }
        } else {
            $results = $this->periodResults;
            if (!$results->daysToWinner) {
                $results->daysToWinner = $days;
            }
            return $results;
        }
    }

    /**
     * Returns even weight on each experience
     * @param array $experiences
     */
    protected function getInitialWeightingRules($experiences) {
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
    protected function getAdjustedWeightingRules($results) {
        $visitsPerExperience = [];
        $conversionsPerExperience = [];
        $xSalesPerExperience = [];
        $revenuePerExperience = [];
        $revStdDevPerExperience = [];
        $revPerConvStdDevPerExperience = []; 
        foreach ($results->experiencesResults as $exRes) {
            /* @var $exRes PeriodTestResults */
            $visitsPerExperience[] = $exRes->visits;
            $conversionsPerExperience[] = $exRes->conversions;
            $xSalesPerExperience[] = $exRes->xSales;
            $revenuePerExperience[] = $exRes->revenue;
            $revStdDevPerExperience[] = $exRes->revStdDev;
            $revPerConvStdDevPerExperience[] = (float) $exRes->revPerConvStdDev;
        }
        $weights = $this->_conditions->strategy->getWeights($visitsPerExperience, $conversionsPerExperience, 
                $xSalesPerExperience, $revenuePerExperience, $revStdDevPerExperience, $revPerConvStdDevPerExperience);
        if (isset($weights)) {
            return array_combine(array_keys($results->experiencesResults), $weights);
        }
    }

    /**
     * Returns the experience key for the experience with the heights weight
     * 
     * @param array $weightingRules
     * @return string
     */
    protected function _getWinnerKey($weightingRules) {
        $winnerKey = null;
        $bestWeight = 9000;
        foreach ($weightingRules as $key => $weight) {
            if ($weight > $bestWeight) {
                $bestWeight = $weight;
                $winnerKey = $key;
            }
        }
        return $winnerKey;
    }

}
