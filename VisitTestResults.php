<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class VisitTestResults {
	/**
	 * The experience that was picked
	 * @var string
	 */
	public $experienceKey;
	 
	/**
	 * Whether or not the visit resulted in a conversion
	 * @var bool
	 */
	public $conversion;
	
	/**
	 * The number of xsales the visit resulted in
	 * @var int
	 */
	public $xSales;
	
	/**
	 * The life time revenue the visit resulted in
	 * 
	 * As this revenue is not realised immediately, you should take a proportion
	 * of it according to the time elapsed
	 * 
	 * @var float
	 */
	public $revenue;
}