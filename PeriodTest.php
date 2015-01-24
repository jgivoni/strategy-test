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
		$conditions = new PeriodTestConditions();
		$conditions->experiences = $this->_conditions->experiences;
		$test = new DayTest($conditions);
		$results = new PeriodTestResults($conditions->experiences);
		$weightingRules = $this->getInitialWeightingRules($conditions->experiences);
		for ($day = 0; $day < $days; $day++) {
			$results->addDayResults($test->getResults($weightingRules, $this->_conditions->visitsPerDay));
			$weightingRules = $this->getAdjustedWeightingRules($results);
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
	protected function getAdjustedWeightingRules($results)
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

}
