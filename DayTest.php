<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'DayTestResults.php';
require_once 'VisitTest.php';
require_once 'VisitTestConditions.php';

class DayTest extends AbstractTest {
	/**
	 * 
	 * @param array $weightingRules
	 * @param int $visits
	 * @return \DayTestResults
	 */
	 public function getResults($weightingRules, $visits) {
		 $conditions = new VisitTestConditions();
		 $conditions->experiences = $this->_conditions->experiences;
		 $conditions->weightingRules = $weightingRules;
		 $results = new DayTestResults($conditions->experiences);
		 $test = new VisitTest($conditions);
		 for ($visit = 0; $visit < $visits; $visit++) {
			 $results->addVisitResults($test->getResults());
		 }
		 return $results;
	 }
}
