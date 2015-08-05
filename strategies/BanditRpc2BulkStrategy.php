<?php

require_once 'strategies/Strategy.php';

class BanditRpc2BulkStrategy extends Strategy
{
    public $relStd;
    
    public $name = 'Binomial bandit on RPC with dynamic priors';
    
	protected $alpha;
	protected $beta;

	protected $subtests = [];
	
	protected $weights = [];
	
	protected $maxBulkSize = 1000;
	
	protected $idleTime = 0;

	public function __construct($relStd = 0.3)
    {
        $this->relStd = $relStd;
    }
    
    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev, $sumSqRev)
    {
		$hash = md5(serialize($visits) . 
			"-" . serialize($conversions) . 
			"-" . serialize($xSales) . 
			"-" . serialize($revenue));
		
		if (isset($this->weights[$hash])) {
			$weights = $this->weights[$hash];
			unset($this->weights[$hash]);
			return $weights;
		} elseif (!isset($this->subtests[$hash])) {
			$divisor = (float) array_sum($revenue) / (float) array_sum($conversions);
			$revenueTransformed = array_map(function($rev) use ($divisor){
				return (float)$rev / (float)$divisor;
			}, $revenue);
			$mean = (float) array_sum($conversions) / (float) array_sum($visits);
			$std = $mean * $this->relStd;
			// Move this to R
			$this->alpha = ($mean*$mean-$mean*$mean*$mean-$mean*$std*$std)/($std*$std);
			$this->beta = ($mean-2*$mean*$mean+$mean*$mean*$mean-$std*$std+$mean*$std*$std)/($std*$std);
			
//alpha = (m^2 - m^3 - m*s^2)/s^2
//beta = (m-2*m^2+m^3-s^2+m*s^2)/s^2


			// Make sure successes are not bigger than trials
			array_walk($visits, function($value, $key) use (&$revenueTransformed) {
				$revenueTransformed[$key] = min([$value, $revenueTransformed[$key]]);
			});

			$this->subtests[$hash] = [
				'trials' => $visits,
				'successes' => $revenueTransformed,
			];
		} else {
			$this->idleTime++;
		}
		
		if (count($this->subtests) >= $this->maxBulkSize || $this->idleTime >= count($this->subtests)) {
			$this->calculateBulkWeights();
		}
    }

    public function displayConfig()
    {
        return "$this->divisor, $this->alpha, $this->beta";
    }

	public function calculateBulkWeights()
	{
		ob_start();
		include "bulk-bandit.r";
		$rScript = ob_get_clean();
		$r = new RAdapter;
		$weights = $r->execute($rScript, RAdapter::FORMAT_CSV);
		$this->weights = array_merge($this->weights, array_combine(array_keys($this->subtests), $weights));
		$this->subtests = [];
		$this->idleTime = 0;
	}
}
