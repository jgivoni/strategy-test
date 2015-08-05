<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'strategies/Strategy.php';

class PoissonCrStrategy extends Strategy
{
    public $name = 'Poisson bandit on CR';
    
    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev)
    {
        // Make sure there are no 0 visits, because it will break the poisson (integrate: a limit is missing)
        $visits = array_map(function($n) {
            return ($n == 0) ? 1 : $n;
        }, $visits);
        $rCommand = "library(bandit); " . 
            "trials=c(" . implode(',', $visits) . "); " .
            "successes=c(" . implode(',', $conversions) . "); " .
            "best_poisson_bandit(successes,trials)";
        $r = new RAdapter();
        $weights = $r->execute($rCommand)[0];
        return array_map(function($weight) {
            return (float) $weight * 10000;
        }, $weights);
    }

}
