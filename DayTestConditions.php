<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'VisitTestConditions.php';

class DayTestConditions extends VisitTestConditions
{
    /**
     * Baseline revenue is the expected revenue if we don't try to improve
     * anything - asuming experiences have even weight
     * @return float
     */
    public function getBaselineRpc()
    {
        $rpcSum = 0;
        foreach ($this->experiences as $e) {
            $rpcSum += $e->rpc;
        }

        return $rpcSum / count($this->experiences);
    }
    
    /**
     * Returns the theoretical epc of the optimal experience
     */
    public function getOptimalRpc()
    {
        $rpc = [];
        foreach ($this->experiences as $e) {
            $rpc[] = $e->rpc;
        }

        return max($rpc);
    }

    public function getBaselineCr()
    {
        $crSum = 0;
        foreach ($this->experiences as $e) {
            $crSum += $e->cr;
        }

        return $crSum / count($this->experiences);
    }

}
