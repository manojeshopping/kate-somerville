<?php
/**
 * Log creation class. Logs entries to database
 * for processing.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Log extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init('ordergroove/log');
	}
	
	public function createLog($activity, $type, $message){
		$this->setActivity($activity);
		$this->setWebsiteId(Mage::app()->getWebsite()->getId());
		$this->setType($type);
		$this->setMessage($message);
		$this->setLogDate(Mage::getModel('core/date')->date());
		$this->save();
		$this->setData(array());
	}
	
	public function getLogDate($format=''){

		if(!$format){
			return $this->getData('log_date');
		}
		
		$logDate	=	$this->getData('log_date');
		
		
		$month	=	$logDate{5}.$logDate{6};
		$day	=	$logDate{8}.$logDate{9};
		$year	=	$logDate{0}.$logDate{1}.$logDate{2}.$logDate{3};
		
		$hour	=	$logDate{11}.$logDate{12};
		$minute	=	$logDate{14}.$logDate{15};
		$second	=	$logDate{17}.$logDate{18};
		
		$timestamp	=	mktime($hour, $minute, $second, $month, $day, $year);
		
		return date($format, $timestamp);
	}
}
