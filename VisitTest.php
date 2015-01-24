<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'VisitTestResults.php';

class VisitTest extends AbstractTest
{

	/**
	 * Number of x-sales per visit
	 */
	const XSALES = 2;

	/**
	 * 
	 * @return VisitTestResult
	 */
	public function getResults()
	{
		// Pick a random experience depending on weights
		$experience = $this->pickExperience();
		$results = new VisitTestResults();
		$results->conversion = $this->isSimulatedConversion($experience);
		$results->xSales = $this->getSimulatedXSales($experience, $results->conversion);
		$results->revenue = $this->getSimulatedRevenue($experience, $results->conversion, $results->xSales);
		return $results;
	}

	/**
	 * 
	 * @return Experience
	 */
	protected function pickExperience()
	{
		$weightingRules = $this->_conditions->weightingRules;
		$pick = mt_rand(0, array_sum($weightingRules));
		foreach ($weightingRules as $key => $weight) {
			$pick = $pick - $weight;
			if ($pick <= 0) {
				return $this->_conditions->experiences[$key];
			}
		}
	}

	/**
	 * Returns whether or not this visit simulated a conversion
	 * 
	 * @param Experience $experience
	 * @return bool
	 */
	protected function isSimulatedConversion(Experience $experience)
	{
		return mt_rand(0, 1) < $experience->cr;
	}

	/**
	 * Returns the number of simulated xsales
	 * 
	 * This depends on if the visit simulated a conversion or not
	 * 
	 * @param Experience $experience
	 * @param bool $conversion
	 * @return int
	 */
	protected function getSimulatedXSales(Experience $experience, $conversion)
	{
		$xSales = 0;
		// You can only have x-sales if there is a conversion
		if ($conversion) {
			for ($x = 0; $x < static::XSALES; $x++) {
				// Divide by the converion rate to compensate for the fact that you 
				// can only have xsales if there is a conversion
				if (mt_rand(0, 1) < $experience->xscr / $experience->cr) {
					$xSales++;
				}
			}
		}
		return $xSales;
	}
	/**
	 * Returns the final revenue this visit will gain
	 * 
	 * This depends on if the visit simulated a conversion or not
	 * and how many x-sales were sold
	 * 
	 * @param Experience $experience
	 * @param bool $conversion
	 * @return int
	 */
	protected function getSimulatedRevenue(Experience $experience, $conversion, $xSales)
	{
		$revenue = 0;
		// You can only have revenue if there is a conversion
		if ($conversion) {
			// Half the revenue is awarded for the conversion
			$revenue = mt_rand(0, $experience->rpc * 2 / $experience->cr / 2);
			for ($x = 0; $x < $xSales; $x++) {
				$revenue += mt_rand(0, $experience->rpc * 2 / $experience->xscr / 2 / static::XSALES);
			}
		}
		return $revenue;
	}

}
