<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DayTestResults {
	/**
	 * Subresults for the individual experiences
	 * @var array of DayTestResults
	 */
	public $experiencesResults = [];
	
	/**
	 * Total visits simulated for the day
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
	 * Initialises a new daily test results
	 * 
	 * Prepares the array of subresults per experience
	 * 
	 * @param array $experiences
	 */
	public function __construct($experiences = []) {
		foreach ($experiences as $key => $experience) {
			 $this->experiencesResults[$key] = new DayTestResults();
		 }
	}
	
	/**
	 * Adds the results for a single visit to the daily visits
	 * 
	 * @param VisitTestResults $results
	 */
	public function addVisitResults(VisitTestResults $results) {
		$this->visits++;
		$this->revenue += $results->revenue;
		$this->conversions += $results->conversion ? 1 : 0;
		$this->xSales += $results->xSales;
		if (isset($this->experiencesResults[$results->experiencesKey])) {
			$this->experiencesResults[$results->experiencesKey]->addVisitResults($results);
		}
		return $this;
	}
}
