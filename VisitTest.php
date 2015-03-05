<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'AbstractTest.php';
require_once 'VisitTestResults.php';

class VisitTest extends AbstractTest
{
    /**
     * 
     * @return VisitTestResults
     */
    public function getResults()
    {
        // Pick a random experience depending on weights
        $experienceKey = $this->pickExperienceKey();
        $results = new VisitTestResults();
        $results->experienceKey = $experienceKey;
        $experience = $this->_conditions->experiences[$experienceKey];
        $results->conversion = $experience->isSimulatedConversion();
        $results->xSales = $experience->getSimulatedXSales($results->conversion);
        $results->revenue = $experience->getSimulatedRevenue($results->conversion, $results->xSales);
        // @todo How to work with deferred or tiered revenue?
        return $results;
    }

    /**
     * 
     * @return string
     */
    protected function pickExperienceKey()
    {
        $weightingRules = $this->_conditions->weightingRules;
        $pick = (float) mt_rand() / (float) mt_getrandmax() * (float) array_sum($weightingRules);
        foreach ($weightingRules as $key => $weight) {
            $pick = $pick - $weight;
            if ($pick <= 0) {
                return $key;
            }
        }
    }

}
