<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'strategies/Strategy.php';

class BanditCrStrategy extends Strategy {
	 public function getWeights($visits,  $conversions, $xSales, $revenue) {
		 // Call R bandit function
		 return $weights;
	 }
}
