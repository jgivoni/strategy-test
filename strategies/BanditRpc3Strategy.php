<?php

require_once 'strategies/Strategy.php';

class BanditRpc3Strategy extends Strategy
{
    public $alpha;
    public $beta;
    
    public $name = '3rd binomial bandit on CR/RPC';
    
    public $divisor = 50;
    
    public function __construct($divisor = null, $alpha = 1.5, $beta = 50)
    {
        if (isset($divisor)) {
            $this->divisor = $divisor;
        }
        $this->alpha = $alpha;
        $this->beta = $beta;
    }
    
    public function getWeights($visits, $conversions, $xSales, $revenue, $sumSqRev)
    {
        $revenueTransformed = array_map(function($rev){
            return (float)$rev / (float)$this->divisor;
        }, $revenue);
       
        // Make sure successes are not bigger than trials
        array_walk($visits, function($value, $key) use (&$revenueTransformed) {
            $revenueTransformed[$key] = min([$value, $revenueTransformed[$key]]);
        });
        
        $rCommand = "library(bandit); " . 
            "trials=c(" . implode(',', $visits) . "); " .
            "successes=c(" . implode(',', $revenueTransformed) . "); " .
            "best_binomial_bandit_sim(successes,trials,$this->alpha,$this->beta)";
        $r = new RAdapter();
        $weights = $r->execute($rCommand, RAdapter::FORMAT_DATAFRAME)[0];
        return array_map(function($weight) {
            return (float) $weight * 10000;
        }, $weights);
    }

    public function displayConfig()
    {
        return "$this->divisor, $this->alpha, $this->beta";
    }

}
