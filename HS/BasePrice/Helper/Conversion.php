<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Helper;

class Conversion extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Array of convertion rates.
     *
     * @var array
     */
    protected $conversionRate = [
        'SQM' => [
            'SQM' => 1,
        ],
        'PCS' => [
            'PCS' => 1,
        ],
        'ML' => [
            'L' => 0.001,
        ],
        'L' => [
            'ML' => 1000,
        ],
        'IN' => [
            'MM' => 25.4,
            'CM' => 2.54,
            'M' => 0.0254,
        ],
        'M' => [
            'CM' => 100,
            'MM' => 1000,
            'IN' => 39.3700787402,
        ],
        'LBS' => [
            'G' => 453.592379,
            'MG' => 453592.379,
            'KG' => 0.453592379,
        ],
        'KG' => [
            'G' => 1000,
            'MG' => 1000000,
            'LBS' => 2.20462257811,
        ],
        'MM' => [
            'CM' => 0.1,
            'M' => 0.001,
            'IN' => 0.0393700787402,
        ],
        'CM' => [
            'MM' => 10,
            'M' => 0.01,
            'IN' => 0.3, 93700787402,
        ],
                'G' => [
            'MG' => 1000,
            'KG' => 0.001,
            'LBS' => 0.00220462257811,
                ],
        'MG' => [
            'G' => 0.001000,
            'KG' => 0.000001,
            'LBS' => 0.00000220462257811,
        ],
    ];

    /**
     * Retrive metric units.
     *
     * @return array
     */
    public function getAvaiableMetricUnits()
    {
        $metrics = [];
        foreach ($this->conversionRate as $key => $conversionRate) {
            $metrics[$key] = __('UNIT_SHORT_'.$key);
        }

        return $metrics;
    }

    /**
     * Get conversion rate from one unit to another.
     *
     * @param string $from
     * @param string $to
     *
     * @return float $rate
     *
     * @throws \Exception
     */
    public function getConversionRate($from, $to)
    {
        if (is_array($to)) {
            // incompatible reference unit defined in the admin
            return false;
        }

        $from = trim(strtoupper($from));
        $to = trim(strtoupper($to));
        if ($from == $to) {
            return 1;
        }

        if (empty($this->conversionRate[$from][$to])) {
            return false;
        }

        return $this->conversionRate[$from][$to];
    }
}
