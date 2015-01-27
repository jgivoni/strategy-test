<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'PeriodTestConditions.php';

class StrategyTestConditions extends PeriodTestConditions
{
    public $daysPerPeriod;

    /**
     * Baseline revenue is the expected revenue if we don't try to improve
     * anything - asuming experiences have even weight
     * @return float
     */
    public function getBaselineRevenue()
    {
        $rpcSum = 0;
        foreach ($this->experiences as $e) {
            $rpcSum += $e->rpc;
        }

        return $rpcSum * $this->daysPerPeriod * $this->visitsPerDay / count($this->experiences);
    }

    public function getOptimalRevenue()
    {
        $bestRpc = 0;
        foreach ($this->experiences as $e) {
            if ($e->rpc >= $bestRpc) {
                $bestRpc = $e->rpc;
            }
        }
        return $bestRpc * $this->daysPerPeriod * $this->visitsPerDay;
    }

    public function getBaselineConversions()
    {
        $crSum = 0;
        foreach ($this->experiences as $e) {
            $crSum += $e->cr;
        }

        return $crSum * $this->daysPerPeriod * $this->visitsPerDay / count($this->experiences);
    }

    public function getOptimalConversions()
    {
        $bestCr = 0;
        foreach ($this->experiences as $e) {
            if ($e->cr >= $bestCr) {
                $bestCr = $e->cr;
            }
        }
        return $bestCr * $this->daysPerPeriod * $this->visitsPerDay;
    }

}
