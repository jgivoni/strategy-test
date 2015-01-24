<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function pl($what, $number, $baseline, $best)
{
    $devation = ((float) $number - (float) $baseline) * 100 / (float) $baseline;
    $optimal = ((float) $number - (float) $baseline) * 100 / ((float) $best - (float) $baseline);
    $numberFormatted = number_format($number, 2);
    $deviationFormatted = number_format($devation, 2);
    $optimalFormatted = number_format($optimal);
    return "{$what}: \t{$numberFormatted} \t($deviationFormatted%) \t$optimalFormatted%\n";
}
