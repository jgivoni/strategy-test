<?php

require_once 'Experience.php';

/**
 * Improved experience simulation
 * 
 */
class Experience2 extends Experience
{
    /**
     * Upgrade ratio (the probability that a main subscription is upgraded or started as monthly)
     * @var float
     */
    protected $ur;
    
    /**
     * X-sale upgrade ratio (the probability that an x-sale subscription is upgraded)
     * @var float
     */
    protected $xur;
    
    /**
     * Average trial price
     * @var float
     */
    protected $trialValue;
    
    /**
     * Average amount an upgraded subscription is worth per month
     * @var float
     */
    protected $monthlyValue;
    
    /**
     * Average number of (re)bills per upgraded subscription (1-?)
     * @var float
     */
    protected $rebills;
    
    /**
     * 
     * @param float $signups Average number of signups per visit (0-1)
     * @param float $xSales Average number of x-sales per conversion (0-2)
     * @param float $upgrades Average number of upgrades per conversion (including subscriptions that didn't start with a trial) (0-1)
     * @param float $xSaleUpgrades Average number of upgrades per x-sale sold (0-1)
     * @param float $trial Average amount a trial subscription is worth
     * @param float $amount Average amount an upgraded subscription is worth per month
     * @param float $rebills Average number of rebills per upgraded subscription (1-?)
     */
    public function __construct($signups, $xSales, $upgrades, $xSaleUpgrades, $trial = 2, $amount = 30, $rebills = 3)
    {
        
        $cr = (float) $signups;
        $xscr = (float) $xSales / self::XSALES * $cr;
        $this->ur = (float) $upgrades;
        $this->xur = (float) $xSaleUpgrades;
        $this->trialValue = (float) $trial;
        $this->monthlyValue = (float) $amount;
        $this->rebills = (float) $rebills;
        $rpc = $cr * ($this->trialValue + $this->ur * $this->monthlyValue * $this->rebills) + 
            $xscr * self::XSALES * ($this->trialValue + $this->xur * $this->monthlyValue * $this->rebills);
        parent::__construct($cr, $xscr, $rpc);
    }

    /**
     * The average rate of conversion
     * @var float
     */
    public $cr;

    /**
     * The average rate of conversion per x-sale
     * @var float
     */
    public $xscr;

    /**
     * Average revenue per visit
     * @var float
     */
    public $rpc;

    /**
     * Returns whether or not this visit simulated a conversion
     * 
     * @return bool
     */
    public function isSimulatedConversion()
    {
        return $this->simulateBinomialOutcome($this->cr);
    }

    /**
     * Returns the number of simulated xsales
     * 
     * This depends on if the visit simulated a conversion or not
     * 
     * @param bool $conversion
     * @return int
     */
    public function getSimulatedXSales($conversion)
    {
        $xSales = 0;
        // You can only have x-sales if there is a conversion
        if ($conversion) {
            for ($x = 0; $x < static::XSALES; $x++) {
                // Divide by the converion rate to compensate for the fact that you 
                // can only have xsales if there is a conversion
                if ($this->simulateBinomialOutcome($this->xscr / $this->cr)) {
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
     * @param bool $conversion
     * @param int $xSales
     * @return int
     */
    public function getSimulatedRevenue($conversion, $xSales)
    {
        $revenue = 0;
        // You can only have revenue if there is a conversion
        if ($conversion) {
            // Trial value
            $revenue += $this->trialValue;
            $rebills = $this->rebills;
            if ($this->simulateBinomialOutcome($this->ur)) {
                // Upgrade
//                $rebills = round($this->bellNumber($this->rebills, 0.5, 1)); // Oops, skewed...
                $revenue += $this->monthlyValue * $rebills;
            }
            for ($x = 0; $x < $xSales; $x++) {
                $revenue += $this->trialValue;
                if ($this->simulateBinomialOutcome($this->xur)) {
                    // Upgrade
//                    $rebills = round($this->bellNumber($this->rebills, 0.5, 1)); // Oops, skewed...
                    $revenue += $this->monthlyValue * $rebills;
                }
            }
        }
        return $revenue;
    }

    /**
     * Returns a random binomial result according to the probability of success
     * @param float $probability The probability that the result is true
     * @return bool
     */
    public function simulateBinomialOutcome($probability)
    {
        return (float) mt_rand() < (float) mt_getrandmax() * (float) $probability;
    }
    
    /**
     * Returns a random number
     * 
     * The random numbers are normally distributed around the 
     * specified mean with the specified standard deviation 
     * 
     * @param float $mean
     * @param float $relativeStdDeviation Deviation relative to mean (0.1 = 10% of $mean = std.dev.)
     * @param float $min Min cutoff value (normal distribution not guaranteed!)
     * @param float $max Max cutoff value (normal distribution not guaranteed!)
     * @return float
     */
    public function bellNumber($mean, $relativeStdDeviation, $min = null, $max = null)
    {
        $stdDeviation = $mean * $relativeStdDeviation;
        $rand1 = (float) mt_rand() / (float) mt_getrandmax();
        $rand2 = (float) mt_rand() / (float) mt_getrandmax();
        $gaussianNumber = sqrt(-2 * log($rand1)) * cos(2 * M_PI * $rand2);
        $randomNumber = ($gaussianNumber * $stdDeviation) + $mean;
        if (isset($min) && $randomNumber < $min) {
            $randomNumber = $min;
        }
        if (isset($max) && $randomNumber > $max) {
            $randomNumber = $max;
        }
        return (float) $randomNumber;
    }

}
