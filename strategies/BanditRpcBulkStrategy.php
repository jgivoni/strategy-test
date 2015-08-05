<?php

require_once 'strategies/Strategy.php';

/**
 * Calculates the weights in bulk based on epc coerced into binomial distribution
 */
class BanditRpcBulkStrategy extends Strategy {

    public $alpha;
    public $beta;
    public $name = 'Bulk binomial bandit on CR/RPC';
    public $divisor = 50;
    protected $subtests = [];
    protected $weights = [];
    protected $maxBulkSize = 1000;
    protected $idleTime = 0;
    protected $rScript = 'bulk-bandit.r';

    public function __construct($divisor = null, $alpha = 1.5, $beta = 50) {
        if (isset($divisor)) {
            $this->divisor = $divisor;
        }
        $this->alpha = $alpha;
        $this->beta = $beta;
    }

    public function getWeights($visits, $conversions, $xSales, $revenue, $stdev, $revPerConvStdev) {
        $hash = md5(serialize($visits) .
                "-" . serialize($conversions) .
                "-" . serialize($xSales) .
                "-" . serialize($revenue));

        if (isset($this->weights[$hash])) {
            $weights = $this->weights[$hash];
            unset($this->weights[$hash]);
            return $weights;
        } elseif (!isset($this->subtests[$hash])) {
            $revenueTransformed = array_map(function($rev) {
                return (float) $rev / (float) $this->divisor;
            }, $revenue);

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

    public function displayConfig() {
        return "$this->divisor, $this->alpha, $this->beta";
    }

    public function calculateBulkWeights() {
        ob_start();
        include $this->rScript;
        $rScript = ob_get_clean();
        $r = new RAdapter;
        $weights = $r->execute($rScript, RAdapter::FORMAT_CSV);
        if (isset($weights)) {
            $this->weights = array_merge($this->weights, array_combine(array_keys($this->subtests), $weights));
            $this->subtests = [];
            $this->idleTime = 0;
        } else {
            die('process halted');
        }
    }

}
