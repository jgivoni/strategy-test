<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'strategies/Strategy.php';

class BanditCrStrategy extends Strategy
{
    public $alpha = 1.5;
    public $beta = 50;
    
    public $name = 'Binomial bandit on CR';
    
    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev)
    {
        $rCommand = "library(bandit); " . 
            "trials=c(" . implode(',', $visits) . "); " .
            "successes=c(" . implode(',', $conversions) . "); " .
            "best_binomial_bandit(successes,trials,$this->alpha,$this->beta)";
        $r = new RAdapter();
        $weights = $r->execute($rCommand)[0];
        if (round(array_sum($weights)*10) != 10) {
            $strategy = new PoissonCrStrategy();
            return $strategy->getWeights($visits, $conversions, $xSales, $revenue);
        }
        return array_map(function($weight) {
            return (float) $weight * 10000;
        }, $weights);
    }

}
