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
     * Number of x-sales per visit
     */
    const XSALES = 2;

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
        $results->conversion = $this->isSimulatedConversion($experience);
        $results->xSales = $this->getSimulatedXSales($experience, $results->conversion);
        $results->revenue = $this->getSimulatedRevenue($experience, $results->conversion, $results->xSales);
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

    /**
     * Returns whether or not this visit simulated a conversion
     * 
     * @param Experience $experience
     * @return bool
     */
    protected function isSimulatedConversion(Experience $experience)
    {
        return (float) mt_rand() < (float) mt_getrandmax() * $experience->cr;
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
                if (mt_rand() < mt_getrandmax() * $experience->xscr / $experience->cr) {
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
            $revenue = $this->bellNumber($experience->rpc / $experience->cr, 10);
            if ($revenue < 0) {
                $revenue = 0; // Just in case, though this means we could distort the real mean
            }
        }
        return $revenue;
    }

    /**
     * Returns a random number
     * 
     * The random numbers are normally distributed around the 
     * specified mean with the specified standard deviation 
     * 
     * @param float $mean
     * @param float $stdDeviation
     * @return float
     */
    function bellNumber($mean, $stdDeviation)
    {
        $rand1 = (float) mt_rand() / (float) mt_getrandmax();
        $rand2 = (float) mt_rand() / (float) mt_getrandmax();
        $gaussianNumber = sqrt(-2 * log($rand1)) * cos(2 * M_PI * $rand2);
        $randomNumber = ($gaussianNumber * $stdDeviation) + $mean;
        return $randomNumber;
    }

}
