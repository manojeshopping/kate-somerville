<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Data extends Mage_Core_Helper_Abstract{
	
	/**
	 * Given an order xml, remove CC data
	 * for logging so that the fields are
	 * not saved in the database.
	 * 
	 * @param $data string
	 * @return string
	 */
	public function clearCcDataOrder($data){
		$data	=	simplexml_load_string($data);
		$data->head->orderCcOwner	=	'********';
		$data->head->orderCcExpire	=	'********';
		$data->head->orderCcNumber	=	'********';
		return $data->asXML();
	}
	
	public function clearCcDataSubscription($data){
		$data = explode("\n", $data);
		
		$hideKeys	=	array(
			'cc_holder',
			'cc_number',
			'cc_exp_date'
		);
		
		foreach($data as $k => $line){
			if(strpos($line, ":") !== FALSE){
				$line	=	explode(":", $line);
				foreach($hideKeys as $key){
					if(strpos($line[0], $key) !== FALSE){
						$data[$k]	=	"\t\t".'"'.$key.'":"*****"';
					}
				}
			}
		}
		return implode("\n", $data);
	}
	
	/**
	 * Given an amount and a percentage, return the given percentage
	 * of the original value in the current websites formatted currency.
	 * Example:	parameters 20, 10.
	 * 			Return 2 because it is 10% of 20.
	 * 
	 * @param float $value
	 * @param float $percentage
	 * @param boolean $currencyFormat | whether to include currency symbol in result
	 * @return float
	 */
	public function calculatePercentage($value, $percentage, $currencyFormat=FALSE){
		$result	=	($percentage*$value) / 100;
		return $result;
		if($currencyFormat){
			$result = Mage::helper('core')->currency($result, true, false);
		}
		else{
			$result = Mage::getModel('directory/currency')->format($result, array('display'=>Zend_Currency::NO_SYMBOL), false);
		}
		return $result;
	}
}
?>
